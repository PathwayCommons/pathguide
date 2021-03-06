use ExtUtils::MakeMaker;
# See lib/ExtUtils/MakeMaker.pm for details of how to influence
# the contents of the Makefile that is written.

require 5.004;
use strict;

my @PREREQS = (
# ["<feature>","<installed module>",<dependency hash>,<install by default, 2=always>];
  ["Core Package","SOAP::Lite",{"XML::Parser" => "2.23","MIME::Base64" => 0, "URI" => 0},2],
  ["Client HTTP support","",{"LWP::UserAgent" => 0},1],
  ["Client HTTPS support","SOAP::Transport::HTTPS::Client",{"Crypt::SSLeay" => 0},0],
  ["Client SMTP/sendmail support","SOAP::Transport::MAILTO::Client",{"MIME::Lite" => 0},1],
  ["Client FTP support","SOAP::Transport::FTP::Client",{"Net::FTP" => 0,"IO::File" => 0},0],
  ["Standalone HTTP server","SOAP::Transport::HTTP::Daemon",{"HTTP::Daemon" => 0},1],
  ["Apache/mod_perl server","SOAP::Transport::HTTP::Apache",{"Apache" => 0},0],
  ["FastCGI server","SOAP::Transport::HTTP::FCGI",{"FCGI" => 0},0],
  ["POP3 server","SOAP::Transport::POP3::Server",{"Net::POP3" => 0,"MIME::Parser" => 0},1],
  ["IO server","SOAP::Transport::IO::Server",{"IO::File" => 0},0],
  ["MQ transport support","SOAP::Transport::MQ",{"MQSeries" => 0},1],
  ["JABBER transport support","SOAP::Transport::JABBER",{"Net::Jabber" => 0},0],
  ["MIME messages","SOAP::MIMEParser",{"MIME::Parser" => 0},1],
  ["SSL Support for TCP Transport","SOAP::Transport::TCP",{"IO::Socket::SSL" => 0},0],
  ["Compression support for HTTP","SOAP::Transport::HTTP",{"Compress::Zlib" => 0},0],
  ["MIME interoperability w/ Axis","SOAP::Lite",{"MIME::Parser" => "6.106"},0],
);

use Getopt::Long;

my $helptext = <<EOI;
Usage: perl Makefile.PL <options>

Possible options are:

  --noprompt            Disable interactive dialog
  --alltests            Perform extra testing
  --help, -?            Display this help text

  [Do not] install prerequisites for appropriate module:

EOI


# Create config parameters using module names and expand help text
# We will create a hash (%config) that has each module => (1|0) for install
my(%options, %config, %has_module_cache);

# Initialize the prereq table and all help text
foreach my $prereq (@PREREQS) {
  my ($feature,$dep,$modules,$default) = @$prereq;
  next unless $dep ne "";
  $prereq->[3] = has_all_modules($modules) unless $prereq->[3] == 2;

  my $module = do { $dep =~ s/::/-/g; $dep };
  my $shortcut = do { (my $t = $module) =~ s/SOAP-(?:Transport-)?//; $t };
  $config{$dep} = has_all_modules($modules);
  $options{"install-$dep|$shortcut!"} = \$config{$dep};
  $helptext .= sprintf "  --[no]install-%-28s --[no]%s\n", $dep, $shortcut;
}

GetOptions(
  'prompt!'   => \(my $prompt = 1),
  'alltests!' => \(my $alltests = 0),
  'help|?'    => \(my $help = 0),
  %options,
);

$help and print($helptext), exit;

ExtUtils::MakeMaker::prompt(<<EOI . "Press <enter> to see the detailed list.");

We are about to install SOAP::Lite and for your convenience will provide
you with list of modules and prerequisites, so you'll be able to choose
only modules you need for your configuration.

XMLRPC::Lite, UDDI::Lite, and XML::Parser::Lite are included by default.
Installed transports can be used for both SOAP::Lite and XMLRPC::Lite.

EOI

# This hash will contain a list of all perl modules we would like to
# explicitly depend upon in our Makefile
my %prereqs;
my $proceed;
do {
  print_prereq_table();
  # Ask the user if this is the configuration they desire
  $proceed = $prompt ?
    ExtUtils::MakeMaker::prompt("\nDo you want to proceed with this configuration?" => 'yes') =~ /^\s*y/i : 1;
  # Loop through each prerequisite and ask the user if they wish to
  # install it or not - reset prereqs, cause they change each time
  %prereqs = ();
  for my $prereq (@PREREQS) {
    my ($feature,$dep,$modules,$default) = @$prereq;
    next unless $dep ne "";
    unless ($proceed || $prereq->[3] == 2) { # need to use $prereq, because we modify actual value
      $prereq->[3] = (ExtUtils::MakeMaker::prompt("Do you plan to use ${feature}?" => ($prereq->[3] ? 'yes' : 'no')) =~ /^\s*(y)/i);
    }
    (%prereqs = (%prereqs, map { $_,$modules->{$_} } keys %$modules)) if $prereq->[3];
  }
} while (!$proceed);

#foreach my $key (keys %prereqs) {
#  print "$key=".$prereqs{$key}."\n";
#}
#exit;

# Warn the user about the testing that will occur
my $noncoretests = $prompt ? ExtUtils::MakeMaker::prompt('
During "make test" phase we may run tests with several SOAP servers
that may take long and may fail due to server/connectivity problems.

Do you want to perform these tests in addition to core tests?', $alltests ? 'yes' : 'no') =~ /^\s*(y)/i : $alltests;

my $tests = join ' ', glob ($noncoretests ? 't/*.t' : 't/0*.t');

WriteMakefile(
    'NAME'	   => 'SOAP::Lite',
    'VERSION_FROM' => 'lib/SOAP/Lite.pm',
    'PREREQ_PM'    => \%prereqs,
    'EXE_FILES'    => ['bin/SOAPsh.pl', 'bin/XMLRPCsh.pl', 'bin/stubmaker.pl'],
    test           => {TESTS => $tests},
);

######################################################
# Supporting subroutines
######################################################
# Maintains a cache of what 3rd party modules you have
# installed
sub has_module {
  my ($mod, $version) = @_;
  $version ||= '';
  # use "require Foo; Exporter::require_version('Foo' => 1)" instead of
  # obvious "use Foo 1". The later doesn't work with MIME::Parser
  # wonder why? --PK
  return ($has_module_cache{"$mod$version"} ||= eval("require $mod; Exporter::require_version('$mod', $version); 1"));
}

# Return 1 if all modules contained in the inputed array
# are installed, 0 otherwise
sub has_all_modules {
  my ($mods) = @_;
  foreach my $mod (keys %$mods) {
    return 0 if !has_module($mod, $mods->{$mod});
  }
  return 1;
}

# Print a single prerequisite to the screen
sub print_prereq {
  my ($feature,$dep,$modules,$install) = @_;
  my $i = 0;
  foreach my $module (keys %$modules) {
    my $detected = (has_module($module, $modules->{$module}) ? "*" : " ");
    printf("%-29s [%s] %-24s %-8s\n",($i++ ? "" : $feature),$detected,$module . ($modules->{$module} ? " (v$modules->{$module})" : ""),($i == 1 ? ($install ? ($install == 2 ? "always" : "[ yes ]") : "[ no ]") : ""));
  }
}

# Print the entire prerequisites table
sub print_prereq_table {
  printf("%-29s %-28s %-8s\n","Feature","Prerequisites","Install?");
  printf("%s %s %s\n","-" x 29,"-" x 28,"-" x 8);
  foreach my $pre (@PREREQS) {
    my ($feature,$dep,$modules,$default) = @{$pre};
    print_prereq(@{$pre});
  }
  print "--- An asterix '[*]' indicates if the module is currently installed.\n";
}

1;

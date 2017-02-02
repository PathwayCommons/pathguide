#!perl -w
#!d:\perl\bin\perl.exe 

# -- SOAP::Lite -- soaplite.com -- Copyright (C) 2001 Paul Kulchenko --

# Google Web API: http://www.google.com/apis/
# NB: Register and get your own key first
# see also:
#   http://interconnected.org/home/more/GoogleSearch.pl.txt
#   http://aaronland.net/weblog/archive/4205
#   http://www.oreillynet.com/cs/weblog/view/wlg/1283

use lib '/home/bader/www/prl/scripts/SOAP-Lite-0.60/lib';
use Mysql;
use SOAP::Lite;

$host='127.0.0.1';
$database='database';
$user='username';
$password='password';
$dbh = Mysql->connect($host, $database, $user, $password);

###
$q = 'SELECT DBID, ShortName, URL from names;';
$sth = $dbh->query($q);

###
$q = "DELETE FROM scores;"; 
$dbh->query($q);

$error_mess = $dbh->errmsg;
if ($error_mess) {
 print "*** Sql-error(1): $error_mess ****";
 print "$arr[1]\t$q\n";
 exit;
}

###
while (@arr = $sth->fetchrow) {
$url = $arr[2];

#print "$url";

if($url eq "http://www.ihop-net.org/UniPub/iHOP/") {
 $url="http://www.pdg.cnb.uam.es/UniPub/iHOP/";
}

if(1==1) {
my $key = '+nc5nqxQFHL2sZxBMF7XSrGS+HyuPKbT'; # <<< put your key here
#my $query = shift || "link:$url";
my $query = shift || "$url -site:$url";

# use GoogleSearch.wsdl file from Google developer's kit
my $google = SOAP::Lite->service('file:./GoogleSearch.wsdl');
my $result = $google->doGoogleSearch(
  $key, $query, 0, 10, 'true', '', 'false', '', 'latin1', 'latin1');

die $google->call->faultstring if $google->call->fault;
#print "About $result->{'estimatedTotalResultsCount'} results.\n";
#print "I looked for this: $result->{'searchQuery'}.\n";
#print "Is this the actual number? $result->{'estimateIsExact'}.\n";
#print "Were results filtered? $result->{'documentFiltering'}.\n";
#print "$arr[0]\t$arr[1]\t$result->{'estimatedTotalResultsCount'}\n";
$google_hits = $result->{'estimatedTotalResultsCount'};

foreach my $results (@{$result->{'resultElements'}}) {
 # Print out the main bits of each result
# print
#  join "\n",
#  $results->{title} || "no title",
#  $results->{URL},
#  $results->{snippet} || 'no snippet',
#  "\n";
  $testURL = $results->{URL};
  if ($testURL =~ /http:\/\/(\w+\.\w+)\.+/) {
    $hasher{$1}++;
  }
}

if (($google_hits > 10) && (keys(%hasher) < 3)) {
  print "BAD!  ";
  $google_hits = -1;
}
undef(%hasher);

### 

$q = "INSERT INTO scores (DBID, Score) VALUES ($arr[0],$google_hits);";
#$q = "UPDATE scores SET Score=$google_hits WHERE DBID=$arr[0];";

print "$arr[1]\t$q\n";
#rob $run_q = $dbh->query($q);

$dbh->query($q);

$error_mess = $dbh->errmsg;
if ($error_mess) {
 print "*** Whoa! $error_mess ****"; 
 exit;
}

#
}
}


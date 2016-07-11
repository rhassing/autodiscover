#!/usr/bin/perl -w

#Declaration of Perl Modules to use.
use strict;
use DBI;
use DBD::mysql;
use Net::SMTP;
use Net::SNMP;

#Declaration of used Constants/Variables
my ($msg_body, $msg_sender , $msg_rcpt, $sthm, $sql, @row, $dbh, $hostname, $output, $snmp, $sysName, $errstr, $r_sysNamei, $getcomm, $getaddress);

#mysql server
my $DBuser2="autodiscover";
my $DBpass2="autodiscover";
my $dbhm2=DBI->connect("dbi:mysql:autodiscover","$DBuser2","$DBpass2",
{
PrintError => 1,
}
);
unless ( $dbhm2 ){
        die("connection does not work properly!");
}

$getcomm = "SELECT value FROM autodiscover.config WHERE what = 'community'";
my ($comm) = $dbhm2->selectrow_array($getcomm);
if($comm eq '') { 
  print "Community not set!";
  exit;
}

$getaddress = "SELECT value FROM autodiscover.config WHERE what = 'address'";
my $sth = $dbhm2->prepare("$getaddress");
$sth->execute();
my $row;
while ($row = $sth->fetchrow_arrayref()) {
    my $addresses = "@$row[0]";

    if($addresses eq '') { 
       print "Address not set!";
       exit;
    } else {
       #print "$addresses \n";

       my $cmd = "fping -a -r 1 -g $addresses -q";
       my @output = `$cmd`;
       chomp @output;

       foreach my $address (@output)
       {

         $sql = "SELECT address FROM db_nagiosql_v2.tbl_host WHERE address = ?";
         @row = $dbhm2->selectrow_array($sql,undef,$address);
         unless (@row) { 

        	my $getexsist = "SELECT ip FROM autodiscover.FoundHosts WHERE ip = '$address'";
	        my ($exsist) = $dbhm2->selectrow_array($getexsist);
	         unless ($exsist) { 
			print "Address not found before: "; 
	
	            my $sysname = &getsnmphostname($address,$comm);
	            if($sysname) {
	                       print "$address not found in exsisting config, hostname: $sysname \n";
					my $query = "insert into autodiscover.FoundHosts(`id`, `ip`, `hostname`, `ignored`) values (?, ?, ?, ?) ";
					# prepare your statement for connecting to the database
					my $statement = $dbhm2->prepare($query);
					# # execute your SQL statement
					$statement->execute('', $address, $sysname, '0');
	            } else {
	                       print "$address not found in exsisting config, snmp does not work \n";
					my $query = "insert into autodiscover.FoundHosts(`id`, `ip`, `hostname`, `ignored`) values (?, ?, ?, ?) ";
					# prepare your statement for connecting to the database
					my $statement = $dbhm2->prepare($query);
					# # execute your SQL statement
					$statement->execute('', $address, 'SNMP NA', '0');
	            }
	
		} else {
	        my $getignored = "SELECT ignored FROM autodiscover.FoundHosts WHERE ip = '$address'";
	        my ($ignored) = $dbhm2->selectrow_array($getignored);
		  if($ignored == '1'){
	          print "$address ignored: $ignored \n";
		  }else{
	          print "$address NOT ignored: $ignored \n";
		  }
	        }	
         }
        }
     } 
}
$dbhm2->disconnect();





sub getsnmphostname {
	my $address = $_[0];
	my $comm = $_[1];
         my $ver='2';
         my $timeout='1';
         my $sysName  = '.1.3.6.1.2.1.1.5.0';

         ($snmp, $errstr) = Net::SNMP->session(
                            -hostname  => $address,
                            -version   => $ver,
                            -community => $comm,
                            -timeout   => $timeout,
                           );
          die("Could not create SNMP session: $errstr\n") unless($snmp);

          my $result = $snmp->get_request(
              -varbindlist => [
              "$sysName",
                ],
             ); 
             my $r_sysName = $result->{"$sysName"};
}


sub getdighostname {
	my $address = $_[0];

    use Net::DNS;
    my $res   = Net::DNS::Resolver->new;
    my $reply = $res->search("$address");

    if ($reply) {
        foreach my $rr ($reply->answer) {
            next unless $rr->type eq "A";
            print $rr->address, "\n";
        }
    } else {
        warn "query failed: ", $res->errorstring, "\n";
    }

}

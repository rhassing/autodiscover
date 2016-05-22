# autodiscover

This is an AutoDiscovery tool which uses a database to store the hosts it has found. These hosts can be transferred to NagiosQL or an Ignored Host list. 

It can be used in combination with Nagios Core with NagiosQL (V 3.2.0) and requires nmap (https://nmap.org/) and Thruk (only uses one stylesheet from Thruk). 

There is one main (perl) script which scans the network and adds the found hosts to a database. It starts with the scan, then checks if the ip address exsists in NagiosQL and it checks its own database. If a host is found which doesn’t exsist in either database, it will check the hostname with a snmpget, using the community given on the webpage. 

Please change the username and password used for the database!

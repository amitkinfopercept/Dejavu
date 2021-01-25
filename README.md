
## Virtual Box Download Link
- Framework/Tool is published here: [Beta V9](http://bit.ly/39iJ2Yy)
- This is a Beta Release and is being shared for testing and suggestions purpose only. Kindly feel free to reach us on bhdresh@gmail.com or naiduharish.r@gmail.com if you have any suggestions/feedback/update.
- Quick setup video guide - https://youtu.be/NOuEGa0241U

## Checksum

**Beta V9**

  - MD5 : 05bb3a59b1f5a8e111c4a1d3cc42dd71
 
 ## Changelog
 - New functionality
 	- Syslog aggregator service for centralised logging and monitoring (Settings >> Update Syslog Details >> Enable log aggregation service)
	- SSH decoy to log attempted username and password
	- Customize TTL and MAC address of decoys
	
	
 - Bug fixes


# DejaVU - Open Source Deception Framework

DejaVu is an open source deception framework which can be used to deploy decoys across the infrastructure. This could be used by the defender to deploy multiple interactive decoys (HTTP Servers, SQL, SMB, FTP, SSH, client side – NBNS) strategically across their network on different VLAN’s. To ease the management of decoys, we have built a web based platform which can be used to deploy, administer and configure all the decoys effectively from a centralized console. Logging and alerting dashboard displays detailed information about the alerts generated and can be further configured on how these alerts should be handled. If certain IP’s like in-house vulnerability scanner, SCCM etc. needs to be discarded, this can be configured which effectively would mean very few false positives. 

Alerts only occur when an adversary is engaged with the decoy, so now when the attacker touches the decoy during reconnaissance or performs authentication attempts this raises a high accuracy alert which should be investigated by the defense. Decoys can also be placed on the client VLAN’s to detect client side attacks such as responder/LLMNR attacks using client side decoys. Additionally, common attacks which the adversary uses to compromise such as abusing Tomcat/SQL server for initial foothold can be deployed as decoys, luring the attacker and enabling detection.

DejaVu has been presented at [Blackhat Arsenal](https://www.blackhat.com/us-18/arsenal.html#dejavu-an-open-source-deception-framework), [Defcon Demo Labs](https://www.defcon.org/html/defcon-26/dc-26-demolabs.html#DejaVU) and [HITB Armory](https://conference.hitb.org/hitbsecconf2018dxb/hitb-armory/)

## Architecture
![Deja Vu Architecture](https://github.com/bhdresh/Dejavu/blob/master/images/DejaVu_Architecture.png)
 - **Host OS:** Primary OS hosting the DejaVU virtual box. Note: Primary   
   host can be OS independent Windows/Linux and can be based on      
   corporate hardening guidelines.
 - **DejaVu Virtual Box:** Debian based image containing open source deception framework to deploy multiple interactive decoys (HTTP Servers, SQL, SMB, FTP, SSH, client side – NBNS, etc.).
 - **Networking**
	 - **Management Interface –** An interface to access web based management console. (Recommended to be isolated from internal network.)
	 - **Decoy Interface –** Trunk/Access interface for inbound connections from different networks towards the interactive decoys. (Recommended to block outbound connections from this interface)
	 - **Virtual Interfaces –** Interfaces bridged with decoy interface to channel traffic towards the decoys. 
- **Server Dockers –** Docker based service containers – HTTP(Tomcat/Apache), SQL, SMB, FTP, SSH, etc.
- **Client Dockers –** Docker based client container – NBNS client
- **Management Console (Web + DB) –** A centralized console to deploy, administer and configure all the decoys effectively along with logging and alerting dashboard to display detailed information about the alerts generated.


## Sneak Peek

<img src="https://github.com/bhdresh/Dejavu/blob/master/images/1.png" width="45%" height="250 px"> <img src="https://github.com/bhdresh/Dejavu/blob/master/images/2.png" width="45%" height="250 px"> <img src="https://github.com/bhdresh/Dejavu/blob/master/images/3.png" width="45%" height="250 px"> <img src="https://github.com/bhdresh/Dejavu/blob/master/images/4.png" width="45%" height="250 px">

## To Do
- [x] Initial Setup Wizard
- [x] Upload custom file structure/pages for web (Apache) decoys
- [x] Persistance on reboot
- [x] Backup/Restore configuration
- [x] Custom Filters to exclude inhouse scanners/asset discovery tool
- [x] Implement syslog
- [x] Event management to manage generated threat events
- [x] Digital breadcrumbs
- [x] HTTPS for Web/Management UI
- [x] Apache docker to serve both HTTP and HTTPS connections
- [x] SNMP, MODBUS and S7COMM server decoys
- [x] Syslog aggregator service for centralised logging and monitoring 
- [x] Customize TTL and MAC address of decoys
- [ ] Code Cleanup and sanitization
- [ ] Add client side decoys generating HTTP, FTP traffic
- [ ] ISO image   
- [ ] Wiki

## Authors
Bhadresh Patel (@bhdresh)

Harish Ramadoss (@hramados)

## Credits

 - AdminLTE for their awesome UI theme
 - Big shout to open source community for previous work on Honeypots/Deception

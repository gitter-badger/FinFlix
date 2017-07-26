So I used /u/gesis scripts as a method to mount google drive to my machine. I modified some of the mounts and scripts to better fit my situation due to the fact my server had a 250g SSD and a 3TB hdd.

I chose to take the unencrypted route, because I support the guys over at plex, I keep testing Plex Cloud. you ask why not just use Plex Cloud? well...it doesnt currently support the amount of customization that I wish to utilize...no channels...monitoring is a little harder.blah blah

I also custom named some of the folders. I.E. `plexdrive` and `plexlocal` 

in testing it just made it superquick to trouble shoot...cause i would always forget that they were originally hidden folders.


Union folder is unionizing `/mnt/hdd/plexdrive/media/` (as read-only) and `/mnt/hdd/plexlocal/` (as read/write) straight into a folder called `/mnt/hdd/media`

read-only avoids your server from writing to the cloud, causing possible API and network issues
read/write lets you write into the union folder locally.


As far as the cronjobs go, play with them as you see fit, I change them ALL the time

I found that syncing every...5th-ish hours gives you some buffertime when most people are watching. so that would be `0 */4 * * *` > `4am 8am 12am 4pm` so on and so forth. NOW saying that. my server has enough bandwidth to do both during the day. so sometimes i just do `@hourly` if my server gets full. 3TB is not as much as you would think...

Plexdrive is a LIFESAVER! i could not recommend it more as the guys who are developing the program keep adding more and more tweaks...I'm on version 3.something, i recommend skipping 4, its not as stable, and 5 looks heavily promising. I've only had it crash once on me...and honestly i think it was just a glitch with the way it caches...otherwise...for using it for 3 months straight...no hiccups..I DO have it cache to the HDD...because the amount of Read-Writes it does for the chunking from gdrive...you'll just KILL an ssd...I have it set to 5MB chunks...so take a 10GB file...do the math... 10,485,760 MB....2,097,152 chunks....and it stays for 24 hours...then gets removed. some have recommended writing to the ram...and even with 24Gb of RAM....i just dont see the point.while yes...no moving parts...i have yet to REALLY experience a bad stream....

I do have plex transcode in the /dev/shm/ but that just forces those transcodes to be near instant.

As far as backing up goes, I have the media folder copied between around 5 different gsuite accounts....i think...i need to check on that.
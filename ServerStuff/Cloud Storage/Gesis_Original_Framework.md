[Gesis Framework](https://www.reddit.com/r/PlexACD/comments/6bmt9s/a_framework_and_tutorial_for_configuring_your_own/)] from r/plexacd

## Introduction

With the availability of "unlimited" cloud-based storage, it is actually pretty easy to set up a streaming media server with nigh infinite storage capacity. The hard part is maintaining it, and that is where some knowledge of scripting goes a long way... and luckily for you, I've already done all the hard parts.
This tutorial will walk you through downloading and installing necessary utilities as well as the configuration of local and remote directory structures, encryption, and timed maintenance tasks.
While the scripts provided support both Amazon Cloud Drive and Google Drive [Gsuite or not], we will only be configuring Google Drive mountpoints. Additionally, while there are numerous options for encrypted filesystems, I will only be covering EncFS as it is the most widely available and perfectly fine for our use-case.
So, without further adieu... let's get on with the show.
## Creating our directories

The first step to setting up your new Privateering Netflix is to create a logical directory structure to store our media, configuration, caches, and remote mounpoints. For the purpose of this tutorial, we will use the following directories.
`~/bin` - This is where we will put any binaries that we download or compile ourselves.
`~/.local-encrypt` - This is the physical location of our locally hosted media
`~/.local-decrypt` - The mountpoint for a virtual filesystem which allows us to view locally hosted content in plaintext.
`~/.gsuite-encrypt` - The mountpoint for our encrypted remote data.
`~/.gsuite-decrypt` - The plaintext version of our encrypted data.
`~/media` - The root of all our Plex libraries. Later, we'll create subdirectories here for categories like tv or movies.
## Downloading essential software

These scripts rely on a few easily installed applications, namely...
1. Plex Media Server - Instructions for download and installation located here.
2. unionfs - Can usually be installed via your distributions package manager.
3. rclone - Download and unzip to ~/bin
4. plexdrive - Download binary and move to ~/bin
5. EncFS - Can be installed via your distributions package manager [usually called "encfs-fuse" or "fuse-encfs"]
6. bc - Can be installed via your distributions package manager.
7. GNU screen - Can be install via your distributions package manager.
## Configuring your $PATH

The easiest way to use any of these scripts, is to have them in your path. You can do that by adding the following line to the bottom of your ~/.bashrc
`PATH=${PATH}:${HOME}/bin/`
## Configuring OAuth Access for GDrive

Log into the Google API Console with your Google account. It doesn’t matter what Google account you use.
Select a project or create a new project.
Under Overview, Google APIs, Google Apps APIs, click “Drive API”, then “Enable”.
Click “Credentials” in the left-side panel (not “Go to credentials”) -> “Create credentials” -> “OAuth client ID”. This will prompt you to set the OAuth consent screen product name, if you haven’t set one already.
Choose an application type of “other”, and click “Create”. (the default name is fine)
This will show you a client ID and client secret. These will be used for accessing your GDrive remote via rclone and plexdrive.
## SSH Tunnelling

When configuring some of this software, you will be prompted to visit a URL something like `http://localhost:9999/auth`. If accessing your server remotely, this will not work. Instead you can create a SSH "tunnel" to access the server and port as though it were your local machine. This can be done using the following...
`ssh -f yourvpsuser@yourvpsipordomain.com -L XXXX:yourvpsipordomain.com:XXXX -N`
where XXXX is the port being asked for.
This will allow you to follow the prompts using your local browser.
## Configuring Rclone

Follow the instructions located [here](https://github.com/jonfinley/FinFlix/blob/master/Server%20Stuff/Cloud%20Storage/rclone_installation.md), however you should provide the clientID and secret from the OAuth configuration above when prompted.
## Configure plexdrive

Run the following commands to begin plexdrive configuration.
`screen -dmS plexdrive plexdrive-linux-amd64 ~/.gsuite-encrypt`
`screen -RD plexdrive`
This will create a background screen session running plexdrive and then attach to it, so you can configure plexdrive. To exit the screen session after configuring plexdrive while still leaving the mount available you can use CTRL+A then D.
When prompted, you should provide the clientID and secret created above. NOTE: This initial run of plexdrive will appear to have hung. It takes quite a while to index all of your files. This is normal. We are using screen so you can continue with the rest of the setup.
## Getting scripts

You can download all the scripts via the public git repo located at git.gesis.pw with the command 
`git clone git://git.gesis.pw:/nimbostratus.git`
After download, you should copy the scripts to your ~/bin directory via the following:
`cp nimbostratus/{rmlocal,cloudupload,umount.remote,mount.remote,makecache,scanlibraries} ~/bin/`
## Configuration

Ok, now that we've completed installtion of the necessary software and scripts, let's get on to configuration...
First, we'll create the configuration directory
`mkdir -p ~/.config/nimbostratus/`
Then copy the configuration file to `~/.config/nimbostratus/config`
`cp nimbostratus/config ~/.config/nimbostratus/config`
If you are following along with this tutorial, the default configuration should be sufficient, however the configuration file is pretty simple to understand if you would like to change settings.
## Setting up encryption using EncFS

To configure the initial encryption for your files, do the following [assuming default configuration]:
`encfs ~/.local-encrypt ~/.local-decrypt`
Use the default options.
Move the `.encfs6.xml` file from `~/.local-encrypt` to your home directory with the following command:
`mv ~/.local-encrypt/.encfs6.xml ~`
If you chose a password during EncFS configuration, edit pass_cmd in the configuration file `~/.config/nimbostratus/config` to reflect your chosen password, for example:
`pass_cmd="echo YOURPASSWORD"`
This will tell encfs to use the command echo YOURPASSWORD in place of typing your password when mounting. You can set this variable to any command or script which returns the plaintext version of your password. I [for example] use the password utility pass to store all my passwords, so I would use something similar to the following:
`pass_cmd="pass plexencfs"`
## Initial mount

After installing/configuring everything, you should be able to run mount.remote all without errors. This will mount all of the appropriate local and remote directories in all the correct places.
## Creating local "fake" cache

The script makecache will create a cache of 0 byte files residing under your cached_media_dir directory. This is the directory where Sonarr/Radarr should store your video libraries [they will actually be stored under `~/.local-encrypt`, but this directory will be read first, keeping APIs from being used just test for the existence of files]
## Configuring Sonarr/Radarr

Sonarr/Radarr/Sickbeard/Couchpotato/Mylar/Headphones/Lidarr/etc... should all be configured to store their libraries under your `cached_media_dir` to reduce API usage.
Configuring Plex Media Server

You should store your libraries in subdirectories for each type of media "TV/Movies/etc.." and Plex should access them from your `plex_media_dir`. This is a union mount which combines both local and remote directories (prioritizing local access first).
## Automatically Running Scripts

All of these scripts are designed to do single tasks and are best run from cron at regular intervals. You should only every need to run mount.remote on reboot of your server, but cloudupdate, rmlocal, and makecache should be run at regular intervals. If you choose to do so, scanlibraries should also be run regularly to update your plex library [though if you are using plexdrive, this isn't needed].
For an example, these are the relevant settings from my crontab file:
```
@reboot /home/gesis/bin/mount.remote all &>> ~/logs/mount.log
@hourly /home/gesis/bin/cloudupload &>> ~/logs/cloudupdates.log
@daily /home/gesis/bin/makecache &>/dev/null
*/17 * * * * /home/gesis/bin/rmlocal &>> ~/logs/nukedupes.log
```
This remounts everything on reboot, tries to upload locally stored files every hour, and attempts to delete already uploaded files every 17 minutes, while updating my "fake" cache once a day.
## Answers to Frequently Asked Questions

### Q: Plex can't see my files?
A: Likely, you are running plex under a different user than the scripts. Set allow_other=1 in the config file.
### Q: How can I use this with Windows?
A: Install GNU/Linux, either on bare-metal or in a VM. I don't use Windows, sorry.
### Q: Why do you use EncFS? Isn't it broken?
A: EncFS is the only in-kernel option for encryption which is friendly with cloud hosting (i.e. encrypting the individual files, instead of creating a large block device). It is also much faster than most of the other options (gocryptfs coming in second). This is one fewer dependency to install, works regardless of the cloud service, and isn't directly tied to any of the other pieces (unlike rclone's crypt implementation, which is no longer convenient, due to declining performance and stability of rclone mounts). While there was an audit of EncFS which showed that the authors did not follow current best-practices, there is nothing making it unsafe for the usage of storing static files on a remote server. The problems that were found concern metadata and a cryptographic weakness when there are numerous iterations of the same file encrypted with the same key. Neither of these cases are of concern when storing video which will be accessed read-only. If you are ultra-paranoid, I would just avoid cloud-storage... none of the alternatives have a favorable audit either.
### Q: Why don't you just use rclone move?
A: My aim for this project is stability. There should never be a moment when files are unavailable. To that aim, I prefer to separate upload and deletion so that the files are always available to Plex without interruption.
### Q: Why Bash?
A: ahem POSIX shell. The primary goal of this project is to create a solid framework for getting cloud storage and Plex working together on many different VPS providers, using POSIX shell gets around needing specific knowledge of the underlying distro, python versions, or other such minutae. A secondary goal of this project is to have the fewest number of dependencies so that disk-space isn't wasted installing a hundred different python libraries or development environments. Shell is simple, readable, and works fine. I have also been writing shell scripts for the past 20+ years, so I'm comfortable with it.
### Q: Why do you only upload one file at a time?
A: The same reason I separate upload and deletion. Uploading a single file allows me to use better file locking so that you're not trying to upload something while reading it. If you need to populate your cloud library quickly, use rclone copy/move/sync for your initial upload.
### Q: Why do you use cron? Can't you use systemd/upstart/openrc/daemontools?
I could use any of those tools to do mostly the same thing, but there is a cron implementation installed on every linux distribution, and it is usually configured to be usable by non-privileged users, unlike most of the other options.
### Q: Thanks for your hard work. Can I buy you coffee/beer/donuts/pizza/hookers & blow?
A: You can toss a couple bucks my way via PayPal, Bitcoin, or Litecoin.
- BTC wallet address: 17KWrRpZgoyvQ7xAcpe3QpvhNkzSeGMU3
- LTC wallet address: LiVV4G3zTVdbkMsZBsGV4YvfGKB2PYUee5

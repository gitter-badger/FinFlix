# Plex Transcode Tweaks
These tweaks have been *near* perfect...I havent had any complaints yet.

- Transcoder quality ===================>   **Prefer higher speed encoding** -
        *its not that i dont want transcodes to be decent quality. im a firm believer in if you have a higher quality to start with, you should get better transcodes. that and i want it to start ASAP*
- Segmented transcoder timeout =========>   **300** -
        *300 seconds is a LONG time*
- Transcoder temporary directory =======>   **/dev/shm** -
        *transcodes happen in the RAM*
- Transcoder default duration ==========>   **120** -
        *2 minutes if the transcoder can't figure out how long the file is.*
- Transcoder default throttle buffer ===>   **600** -
        *600 seconds in buffer time before you the transcoder slows down it's speed*
- Background transcoding x264 preset ===>   **Very fast** -
        *shrug, honeslty seen no quality loss* -
- Maximum simultaneous video transcode =>   **Unlimited** -
        *YLMV!*

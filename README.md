## Technical stack

Backend: plain php 7.0+ version, mysql as a database.<br>
Frontend: bootstrap for markup data + vue.js for handle/request data

## Installation

After pushing repository put it somewhere in your htdocs directory to get access to it through browser.
After that you have to access it <em>not from your local computer</em>, cause to get the client ip(to get timezone later) i am using [code]SERVER['REMOTE_ADDR'][/code]
and in case of accessing from local compture your remote_addr will be "::1"(or the 127.0.0.1) which are not public and which means it will not be possible
to fetch timezone from this. Either you can just directly set your ip address for test into getSecondTestResult() function in calculation.php, into FetchTimeZoneByIp.

After accessing site you will be able to check first two tests, the third one is in file db.sql under root project folder.

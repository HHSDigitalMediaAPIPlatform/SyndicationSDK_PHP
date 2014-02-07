#!/bin/sh
#!/bin/sh
php -S `ifconfig en0 | grep inet | grep -v inet6 | awk '{print $2}'`:3333 -t public/

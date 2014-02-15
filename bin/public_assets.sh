#!/bin/sh
#!/bin/sh
#ophp -S `ifconfig en0 | grep inet | grep -v inet6 | awk '{print $2}'`:3333 -t public/
ifc=`ifconfig en0 | grep inet | grep -v inet6 | awk '{print $2}'`; 
if [ -z "$ifc" ]; then
    ifc=`ifconfig en1 | grep inet | grep -v inet6 | awk '{print $2}'`; 
    if [ -z "$ifc" ]; then
        echo ' !!! Cannt find public IP address';
        ifc='localhost'
    fi;
fi;
echo "php -S $ifc:3333 -t public/";
php -S $ifc:3333 -t public/

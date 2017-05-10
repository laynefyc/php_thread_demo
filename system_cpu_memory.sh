echo "******* CPU INFO ****** "
cat /proc/cpuinfo |grep "model name" && cat /proc/cpuinfo |grep "physical id"
echo "******* MEMORY INFO ****** "
cat /proc/meminfo |grep MemTotal
echo "******* OTHER INFO ****** "
df -h

echo "******* CPU INFO ****** \n"
cat /proc/cpuinfo |grep "model name" && cat /proc/cpuinfo |grep "physical id"
echo "******* MEMORY INFO ****** \n"
cat /proc/meminfo |grep MemTotal
echo "******* OTHER INFO ****** \n"
df -h

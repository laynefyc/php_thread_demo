#!/bin/bash
te_abb=$(df -h |awk '/^\/dev\/+/ { print $1 }')
echo $te_abb
sum=0
for num in {1..6}; do
    te_tmp=$(hdparm -t $te_abb | awk '{split($0,b,"=");print b[2]}' | awk '/[0-9.]+/{ print $1 }')
    echo $te_tmp
    sum=$(echo $sum+$te_tmp | bc )
done
echo "COUNT:"$sum
echo "$sum / 6.0" | bc

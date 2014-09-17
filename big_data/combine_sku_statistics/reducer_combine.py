#!/usr/bin/env python
import sys

current_sku=None
current_count = 0
country_dic = {}

for line in sys.stdin:
    line = line.strip()
    if '\001' in line:
        line = line.split('\001')
    else:
        line = line.split('\t')

    sku = line[0].strip()
    country = line[1].strip()
    count = int(line[2].strip())

   
    if current_sku == sku:
        #print 'test 1'
        #print count
        current_count += count
        if country_dic.has_key(country):
            country_dic[country] += count
        else:
            country_dic[country]=count 
    else:
        if current_sku and current_count >1:
           print '%s\t%s\t%s\t%s'%(current_sku,current_count,current_count/float(len(country_dic)),str(country_dic));
        #print 'test 2'
        country_dic={}
        current_sku = sku
        current_count = count 
        country_dic[country]=count
        #print current_sku,country_dic,current_count,count


if current_sku == sku:
    print '%s\t%s\t%s\t%s'%(current_sku,current_count,current_count/float(len(country_dic)),str(country_dic));
   

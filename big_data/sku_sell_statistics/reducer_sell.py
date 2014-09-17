#!/usr/bin/env python
import sys

current_email=None
email=None
sku_list=[]
combine_sku=None
pre_country=None


for line in sys.stdin:
    line = line.strip()
    if '\001' in line:
        line = line.split('\001')
    else:
        line = line.split('\t')
    email = line[0].strip()
    country = line[1].strip()
    sku = line[4].strip()
 

    if current_email == email:
        #print 'test 2'
        #print current_email,sku_list,sku
        if sku not in sku_list:
            sku_list.append(sku)

    else:
        if current_email:
           #print 'test 3'
           #print sku_list
           if len(sku_list) > 1:
               #print current_email,sku_list
               for i in range(len(sku_list)-1):
                   for j in range(i+1,len(sku_list)):
                       #print 'the i is %s and the j is %s'%(i,j)
                       if sku_list[i] == sku_list[j]:
                           continue
                       if len(sku_list[i]) < len(sku_list[j]):
                           combine_sku = sku_list[i]+'+'+sku_list[j]
                       elif len(sku_list[i]) == len(sku_list[j]) and sku_list[i]<sku_list[j]:
                           combine_sku = sku_list[i]+'+'+sku_list[j] 
                       else:
                           combine_sku = sku_list[j]+'+'+sku_list[i] 
                       print '%s\t%s\t%s' % (combine_sku,pre_country,'1')
           sku_list=[] 
        #print 'test 4'
        current_email = email
        pre_country = country
        sku_list.append(sku)
        #print current_email,pre_country,sku_list


if current_email == email:
    if len(sku_list)>1:
        for i in range(len(sku_list)-1):
            for j in range(i+1,len(sku_list)):
                if sku_list[i] != sku_list[j]:
                    continue
                if len(sku_list[i]) < len(sku_list[j]): 
                    combine_sku = sku_list[i]+'+'+sku_list[j]
                elif len(sku_list[i]) == len(sku_list[j]) and sku_list[i]<sku_list[j]:
                    combine_sku = sku_list[i]+'+'+sku_list[j] 
                else:
                    combine_sku = sku_list[j]+'+'+sku_list[i] 
                print '%s\t%s\t%s' % (combine_sku,pre_country,'1')

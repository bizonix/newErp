#!/usr/bin/env python
import sys
import re

for line in sys.stdin:
    if '\001' in line:
        line=line.strip().split('\001')
    else:
        line=line.strip().split('\t')
       # print type(combine_sku),type(ebay_amount),type(sku_set)
    status=line[2].strip()
    combine=line[4].strip()
    

    
    if int(status) in (0,615,617,625,663,690):
        continue
    if combine =='1':
        continue
 
  #  print line
  #  print status,line[5].strip()
  #  print addtime
  #  print combine
  #  print amount,line[5].strip()
    combine_sku=line[5].strip()
    sku_set=line[7].strip()
    ebay_amount=line[6].strip()
    #print combine_sku,ebay_amount,sku_set
    #print type(combine_sku),type(ebay_amount),type(sku_set)
    if (combine_sku=='' or combine_sku=='null') or (len(ebay_amount)==0 and len(sku_set)==0) or len(ebay_amount)==0:
       continue
    if sku_set=='null':
       print "%s\t%s\t%s"%(combine_sku,ebay_amount,'null')
    #   print ''
    else: 
        if ',' not in sku_set:
            try:
                sku_tunple=sku_set.split("*")
                #print sku_tunple
                sku=''.join(sku_tunple[0])
                sku_amount=''.join(sku_tunple[1])
                sku_amount=sku_amount.strip()
                sku_amount=re.findall(r'\A\d+',sku_amount,re.M)
                sku_amount=''.join(sku_amount)
	        #print sku_amount
                #print sku_set,sku_tunple,sku,sku_amount
                #print "%s\t%s\t%s"%(combine_sku,int(ebay_amount)*int(sku_amount),sku_tunple[0]) 
            except IndexError:
                sku_amount=1 
            print "%s\t%s\t%s"%(combine_sku,int(ebay_amount)*int(sku_amount),sku_tunple[0]) 

        else:
            sku_set=sku_set.split(",")
            sku_count_in_set = len(sku_set);
            for i in range(sku_count_in_set):
                sku_tunple=sku_set[i].split("*")
                sku=''.join(sku_tunple[0])
                sku_amount=''.join(sku_tunple[1])
                sku_amount=re.findall(r'\A\d+',sku_amount,re.M)
                sku_amount=''.join(sku_amount)
                sku_amount=sku_amount.strip()
                print "%s\t%s\t%s"%(combine_sku,int(ebay_amount)*int(sku_amount),sku_tunple[0]) 

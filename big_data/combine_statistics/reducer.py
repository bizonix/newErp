#!/usr/bin/env python
import sys
import re

current_sku=None
count_1=0
count_7=0
count_30=0
sku=None
pre_sku_set=None

for line in sys.stdin:
    line = line.strip()
    line = line.split('\t')
    
   
    paidtime=line[1].strip()
    addtime=int(line[2].strip())
    sku=line[0].strip()
    ebay_amount=int(line[3].strip())
    sku_set=line[4].strip()
    try:
        int(paidtime)
    except ValueError:
        paidtime=addtime 
   
    if current_sku == sku:
        if (paidtime>1399132800 and paidtime<1399219199) or (addtime > 1399132800  and addtime< 1399219199):
            count_1 += ebay_amount
         
        if (paidtime > 1398614399 and paidtime < 1399219199) or (addtime >1398614399  and addtime < 1399219199 ):
            count_7 += ebay_amount
        if (paidtime > 1396627199 and paidtime < 1399219199) or (addtime >1396627199 and addtime < 1399219199 ):
            count_30 += ebay_amount 
    else:
        if current_sku and (count_1 !=0 or count_7 != 0 or count_30 != 0) and pre_sku_set != 'null':
            if ',' not in pre_sku_set:
                try:
                    sku_tunple=pre_sku_set.split("*")
                    sku_key=''.join(sku_tunple[0])
                    sku_amount=''.join(sku_tunple[1])
                    sku_amount=sku_amount.strip()
                    sku_amount=re.findall(r'\A\d+',sku_amount,re.M)                
                    sku_amount=''.join(sku_amount)
                    print '%s\t%s\t%s\t%s\t%s'%(sku_key,current_sku,int(sku_amount)*count_1,int(sku_amount)*count_7,int(sku_amount)*count_30)
                except IndexError,AttributeError:
                    sku_amount=1
                    print '%s\t%s\t%s\t%s\t%s'%(sku_key,current_sku,int(sku_amount)*count_1,int(sku_amount)*count_7,int(sku_amount)*count_30)
                count_1=0
                count_7=0
                count_30=0
            else:
                try:
                    pre_sku_set=pre_sku_set.split(",")
                    sku_count_in_set = len(pre_sku_set);
                    for i in range(sku_count_in_set):
                        sku_tunple=pre_sku_set[i].split("*")
                        sku_key=''.join(sku_tunple[0])
                        sku_amount=''.join(sku_tunple[1])
                        sku_amount=re.findall(r'\A\d+',sku_amount,re.M)
                        sku_amount=''.join(sku_amount)
                        sku_amount=sku_amount.strip()
                        print '%s\t%s\t%s\t%s\t%s'%(sku_key,current_sku,int(sku_amount)*count_1,int(sku_amount)*count_7,int(sku_amount)*count_30)
                except ValueError,IndexError:
                    continue
                count_1=0
                count_7=0
                count_30=0
        elif current_sku and (count_1 !=0 or count_7 !=0 or count_30 !=0) and pre_sku_set == 'null':
            print '%s\t%s\t%s\t%s\t%s'%(current_sku,'null',count_1,count_7,count_30)
            count_1=0
            count_7=0
            count_30=0
       
        current_sku = sku
        pre_sku_set = sku_set 
        if (paidtime>1399132800 and paidtime<1399219199) or (addtime > 1399132800  and addtime< 1399219199):
            count_1 += ebay_amount
        if (paidtime > 1398614399 and paidtime < 1399219199) or (addtime >1398614399  and addtime < 1399219199 ):
            count_7 += ebay_amount
        if (paidtime > 1396627199 and paidtime < 1399219199) or (addtime >1396627199 and addtime < 1399219199 ):
            count_30 += ebay_amount 

if current_sku == sku and sku_set != 'null': 
    if ',' not in sku_set:
        try:
            sku_tunple=sku_set.split("*")
            sku_key=''.join(sku_tunple[0])
            sku_amount=''.join(sku_tunple[1])
            sku_amount=sku_amount.strip()
            sku_amount=re.findall(r'\A\d+',sku_amount,re.M)
            sku_amount=''.join(sku_amount)
        except IndexError:
            sku_amount=1
        print '%s\t%s\t%s\t%s\t%s'%(sku_key,current_sku,int(sku_amount)*count_1,int(sku_amount)*count_7,int(sku_amount)*count_30)
    else:
       sku_set=sku_set.split(",")
       sku_count_in_set = len(sku_set);
       for i in range(sku_count_in_set):
           sku_tunple=sku_set[i].split("*")
           sku_key=''.join(sku_tunple[0])
           sku_amount=''.join(sku_tunple[1])
           sku_amount=re.findall(r'\A\d+',sku_amount,re.M)
           sku_amount=''.join(sku_amount)
           sku_amount=sku_amount.strip()
           print '%s\t%s\t%s\t%s\t%s'%(sku_key,current_sku,int(sku_amount)*count_1,int(sku_amount)*count_7,int(sku_amount)*count_30)

if current_sku == sku and sku_set =='null':
    print '%s\t%s\t%s\t%s\t%s'%(current_sku,'null',count_1,count_7,count_30)

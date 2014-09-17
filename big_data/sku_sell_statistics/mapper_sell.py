#!/usr/bin/env python
import sys
import re

'''
sku_combine
ebay_ordersn:0,ebay_paidtime:1,ebay_usermail:2,ebay_countryname:3,ebay_status:4,ebay_addtime:5,ebay_combine:6,sku:7,ebay_amount:8,goods_sncombine:9
'''

for line in sys.stdin:
    line = line.strip()
    if '\001' in line:
        line = line.split('\001')
    else:
        line = line.split('\t') 

    status = line[4].strip()
    combine = line[6].strip()
    combine_sku = line[9].strip()
    if combine_sku != 'null':
        continue
    if line[2] == '':
        continue

    if int(status) in (0,615,617,625,663,690):
        continue
    if combine == '1':
        continue
     
    mail = line[2].strip()
    country = line[3].strip()
    paidtime = line[1].strip()
    addtime = line[5].strip()
    sku = line[7].strip()
    print '%s\t%s\t%s\t%s\t%s'%(mail,country,paidtime,addtime,sku)


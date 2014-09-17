#!/usr/bin/env python
'''
ebay_order_detail
ebay_ordersn:0,ebay_paidtime:1,ebay_usermail:2,ebay_countryname:3,ebay_status:4,ebay_addtime:5,ebay_combine:6,sku:7,ebay_amount:8

eaby_productcombine
goods_sn:1,goods_sncombine:2
'''
import sys

eod_len=7
ep_len=17
ebay_order_detail_value=None
ebay_productcombine_value=None

for line in sys.stdin:
    line = line.strip()
    if '\001' in line:
        line = line.split('\001')
    else:
        line = line.split('\t')
    s_len = len(line)

    if s_len == 17 and (line[1].strip()!=''):
        ebay_productcombine_value='1'+'\001'+line[2].strip()
        print '%s\t%s'%(line[1].strip(),ebay_productcombine_value)
    elif s_len ==9 and (line[7].strip()!=''):
        ebay_order_detail_value='2'+'\001'+line[0].strip()+'\001'+line[1].strip()+'\001'+line[2].strip()+'\001'+line[3].strip()+'\001'+line[4].strip()+'\001'+line[5].strip()+'\001'+line[6].strip()+'\001'+line[8].strip()
        print '%s\t%s'%(line[7].strip(),ebay_order_detail_value)
    else:
        continue

#!/usr/bin/env python
'''
ebay_order
ebay_ordersn:1,ebay_paidtime:9,ebay_status:23,ebay_addtime:25,ebay_combine:27

ebay_orderdetail
ebay_ordersn:2,sku:6,ebay_amount:8
'''

import sys

eo_len=76
eod_len=28
for line in sys.stdin:
   ebay_order_value=None
   ebay_orderdetail_value=None
   line = line.strip()
   line=line.split('\001')
   s_len = len(line)

   if (s_len == 76) and (line[1].strip()!=''):
      ebay_order_value='1'+'\001'+line[9].strip()+'\001'+line[12].strip()+'\001'+line[18].strip()+'\001'+line[23].strip()+'\001'+line[25].strip()+'\001'+line[27].strip() 
      #print '%s\t%s'%(line[1].strip(),ebay_order_value)
   elif (s_len == 28) and (line[2].strip()!=''):
      ebay_orderdetail_value='2'+'\001'+line[6].strip()+'\001'+line[8].strip()
      #print '%s\t%s'%(line[2].strip(),ebay_orderdetail_value)
   else:
      #print '%s\t%s\t%s\t%s'%(line[2],line[6],line[8],s_len)
      continue
  
     
      
      

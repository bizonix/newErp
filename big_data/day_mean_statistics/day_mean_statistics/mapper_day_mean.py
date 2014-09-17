#!/usr/bin/env python
import sys

'''
sku_7
sku:0,amount:1,weight:2
sku_8
......
sku_15
......
'''

for line in sys.stdin:
    line = line.strip()
    if '\001' in line:
        line = line.split('\001')
    else:
        line = line.split('\t')

    time_com = int(line[2].strip())
    value_list=None
    if time_com == 7:
        value_list='1'+'\001'+line[1].strip()+'\001'+'0.7'
        print '%s\t%s'%(line[0].strip(),value_list)
    elif time_com == 8:
        value_list='2'+'\001'+line[1].strip()+'\001'+'0.2'
        print '%s\t%s'%(line[0].strip(),value_list)
    elif time_com == 15:
        value_list='3'+'\001'+line[1].strip()+'\001'+'0.1'
        print '%s\t%s'%(line[0].strip(),value_list)
    else:
        continue
    
        

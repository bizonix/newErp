#!/usr/bin/env python
'''
ebay_order_detail
ebay_ordersn:0,ebay_paidtime:1,ebay_usermail:2,ebay_countryname:3,ebay_status:4,ebay_addtime:5,ebay_combine:6,sku:7,ebay_amount:8
'''
import sys  
  
current_ordersn = None  
ordersn = None  
left_info=[]
right_info=[]
value_list=None

for line in sys.stdin:  
    line = line.strip()  
 
    ordersn, value_list = line.split('\t',1)  

    value_list = value_list.split('\001')

    if current_ordersn == ordersn:  
        if int(value_list[0]) == 1:
            left_info.append(value_list)
        elif int(value_list[0]) == 2:
            right_info.append(value_list)       
    else:  
        if current_ordersn: 
            if len(left_info)==1 and len(right_info)>0:
                for i in range(len(right_info)):
                    print '%s\001%s\001%s\001%s\001%s\001%s\001%s\001%s\001%s' % (current_ordersn,left_info[0][1],left_info[0][2],left_info[0][3],left_info[0][4],left_info[0][5],left_info[0][6],right_info[i][1],right_info[i][2])  
                left_info=[]
                right_info=[]
            else:
                left_info=[]
                right_info=[]
        if int(value_list[0]) == 1:
            left_info.append(value_list)
        elif int(value_list[0]) == 2:
            right_info.append(value_list)
        current_ordersn = ordersn  

if len(left_info)==1 and len(right_info)>0:
         for i in range(len(right_info)):
             print '%s\001%s\001%s\001%s\001%s\001%s\001%s\001%s\001%s' % (current_ordersn,left_info[0][1],left_info[0][2],left_info[0][3],left_info[0][4],left_info[0][5],left_info[0][6],right_info[i][1],right_info[i][2])  

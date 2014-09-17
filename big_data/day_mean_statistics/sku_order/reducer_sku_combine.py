#!/usr/bin/env python
import sys  
  
current_sku = None  
sku = None  
left_info=[]
right_info=[]
value_list=None

for line in sys.stdin:  
    line = line.strip()  
 
    sku ,value_list = line.split('\t',1)  

    value_list = value_list.split('\001')

    if current_sku == sku:  
        if int(value_list[0]) == 1:
            left_info.append(value_list)
        elif int(value_list[0]) == 2:
            right_info.append(value_list)       
    else:  
        if current_sku: 
            if (len(left_info)==1 and len(right_info)>0): 
                for i in range(len(right_info)):
                    print '%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s' % (right_info[i][1],right_info[i][2],right_info[i][3],right_info[i][4],right_info[i][5],right_info[i][6],right_info[i][7],current_sku,right_info[i][8],left_info[0][1])
                left_info=[]
                right_info=[]
            elif len(left_info)==0 and len(right_info)>0:
                for i in range(len(right_info)):
                    print '%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s' % (right_info[i][1],right_info[i][2],right_info[i][3],right_info[i][4],right_info[i][5],right_info[i][6],right_info[i][7],current_sku,right_info[i][8],'null')
                left_info=[]
                right_info=[]
            else:
                left_info=[]
                right_info=[]
        if int(value_list[0]) == 1:
            left_info.append(value_list)
        elif int(value_list[0]) == 2:
            right_info.append(value_list)
        current_sku = sku  

if len(left_info)==1 and len(right_info)>0:
     for i in range(len(right_info)):
         print '%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s' % (right_info[i][1],right_info[i][2],right_info[i][3],right_info[i][4],right_info[i][5],right_info[i][6],right_info[i][7],current_sku,right_info[i][8],left_info[0][1])
elif len(left_info)==0 and len(right_info)>0:
     for i in range(len(right_info)):
         print '%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s' % (right_info[i][1],right_info[i][2],right_info[i][3],right_info[i][4],right_info[i][5],right_info[i][6],right_info[i][7],current_sku,right_info[i][8],'null')

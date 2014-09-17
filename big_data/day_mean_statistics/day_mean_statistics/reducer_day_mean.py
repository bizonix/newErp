#!/usr/bin/env python
import sys  
  
current_sku = None  
amount_7=0
amount_8=0
amount_15=0
weight_7=0
weight_8=0
weight_15=0
sku = None  
  
# input comes from STDIN  
for line in sys.stdin:  
    # remove leading and trailing whitespace  
    line = line.strip()  
  
    # parse the input we got from mapper.py  
    #print line
    sku,amount_list = line.split('\t',1)  
    amount_list = amount_list.split('\001')
  
    # convert count (currently a string) to int  
    try:  
        amount = int(amount_list[1])  
    except ValueError:  
        # count was not a number, so silently  
        # ignore/discard this line  
        continue  
  
    # this IF-switch only works because Hadoop sorts map output  
    # by key (here: word) before it is passed to the reducer  
    if current_sku == sku:  
        if int(amount_list[0]) == 1:
           amount_7=int(amount_list[1].strip())
           weight_7=float(amount_list[2].strip())
        elif int(amount_list[0]) ==2:
           amount_8=int(amount_list[1].strip())
           weight_8=float(amount_list[2].strip())
        elif int(amount_list[0]) ==3:
           amount_15=int(amount_list[1].strip())
           weight_15=float(amount_list[2].strip())
    else:  
        if current_sku:  
            print '%s\t%s\t%s\t%s\t%s' % (current_sku,amount_7,amount_8,amount_15,(amount_7*weight_7/7+amount_8*weight_8/8+amount_15*weight_15/15))  
            amount_7=0
            amount_8=0
            amount_15=0
        if int(amount_list[0]) == 1:
           amount_7=int(amount_list[1].strip())
           weight_7=float(amount_list[2].strip())
        elif int(amount_list[0]) ==2:
           amount_8=int(amount_list[1].strip())
           weight_8=float(amount_list[2].strip())
        elif int(amount_list[0]) ==3:
           amount_15=int(amount_list[1].strip())
           weight_15=float(amount_list[2].strip())
        current_sku = sku  
  
# do not forget to output the last word if needed!  
if current_sku == sku:  
    print '%s\t%s\t%s\t%s\t%s' % (current_sku,amount_7,amount_8,amount_15,(amount_7*weight_7/7+amount_8*weight_8/8+amount_15*weight_15/15))  

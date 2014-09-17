#!/usr/bin/env python
from operator import itemgetter  
import sys  
  
current_sku = None  
current_count = 0  
sku = None  
  
# input comes from STDIN  
for line in sys.stdin:  
    # remove leading and trailing whitespace  
    line = line.strip()  
  
    # parse the input we got from mapper.py  
    #print line
    sku,count = line.split('\t',1)  
  
    # convert count (currently a string) to int  
    try:  
        count = int(count)  
    except ValueError:  
        # count was not a number, so silently  
        # ignore/discard this line  
        continue  
  
    # this IF-switch only works because Hadoop sorts map output  
    # by key (here: word) before it is passed to the reducer  
    if current_sku == sku:  
        current_count += count  
    else:  
        if current_sku:  
            # write result to STDOUT  
            print '%s\t%s\t%s' % (current_sku, current_count,'8')  
        current_count = count  
        current_sku = sku  
  
# do not forget to output the last word if needed!  
if current_sku == sku:  
    print '%s\t%s\t%s' % (current_sku, current_count,'8')  

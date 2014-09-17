package com.honux.eob;

import java.io.IOException;

import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Mapper;

public class EbayOrderDetailMapper extends
		Mapper<Object, Text, Text, Text> {

	enum counter{
		LINESKIP,//³ö´íÐÐ
	}
	
	public void map(Object key, Text value, Context context)
			throws IOException, InterruptedException {	
		String str = value.toString();
		//System.out.print(str);
		String[] strArray=str.split("\001");
		//System.out.print(strArray);
		try{
			System.out.printf("%s,%s,%s,%s and the lenght %s\n",strArray[0],strArray[1],strArray[2],strArray[3],strArray.length);
			if(strArray.length==76 && !strArray[1].equals("")){
				context.write(new Text(strArray[1]+"\t"),new Text("1+"+strArray[12].trim()+"+"+strArray[18].trim()+"+"+strArray[23].trim()+"+"+strArray[25].trim()+"+"+strArray[27].trim()));
			}else if((strArray.length==28 ||strArray.length==27) && !strArray[2].equals("")){
				context.write(new Text(strArray[2]+'\t'),new Text("2+"+strArray[6]+"+"+strArray[8]));
			}else{
				//System.out.printf("%s\n", "the data is wrong!");
				//System.exit(1);
			}
		}catch(java.lang.ArrayIndexOutOfBoundsException e){
			/*
			for (String ebay_s:strArray){
				System.out.print(ebay_s+" ");
			}
			*/
			context.getCounter(counter.LINESKIP).increment(1);
			return;
		}
		//System.out.printf("the line has skiped is %s \n",counter.LINESKIP);
	}

}

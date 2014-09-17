package com.honux.sku;

import java.io.IOException;

import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Mapper;


public class SkuOrderMapper extends Mapper<Text, Text, Text, Text> {
	
	enum counter{
		LINESKIP,//³ö´íÐÐ
	}

	public void map(Text key, Text value, Context context)
			throws IOException, InterruptedException {
		String str = value.toString();
		String[] strArray = str.split("\001");
		try{
			if(strArray.length == 17){
				String strKey = strArray[1].trim();
				if(!strKey.equals("")){
					context.write(new Text(strKey), new Text("1"+"\001"+strArray[2].trim()));
				}
			}else if(strArray.length == 9){
				String strKey = strArray[7].trim();
				if(!strKey.equals("")){
					String strValue = new String();
					int i=0;
					strValue.concat("2");
					strValue.concat("\001");
					for(String Value:strArray){
						if (i != 7){
							strValue.concat("\001");
							strValue.concat(Value);
						}
						i++;
					}
					context.write(new Text(strKey), new Text(strValue));
				}
			}
		}catch(java.lang.ArrayIndexOutOfBoundsException e){
			context.getCounter(counter.LINESKIP).increment(1);
			return;
		}
	}
}

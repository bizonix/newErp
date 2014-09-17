package com.honux.skuUserCollections;

import java.io.IOException;

import org.apache.hadoop.io.LongWritable;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Mapper;

public class skuUserMapper extends Mapper<LongWritable, Text, Text, Text> {

	public void map(Text key, Text value, Context context)
			throws IOException, InterruptedException {
		String str = value.toString();
		String[] strArray = str.split("\001");
		
		String status = new String(strArray[4].trim());
		int istatus = Integer.parseInt(status);
		String combine = new String(strArray[6].trim());
		String combineSku= new String(strArray[9].trim());
		
		if(combineSku.equals("null")){
			if(!strArray[2].equals("")){
				if(!combine.equals("1")){
					if(istatus!=0 && istatus != 615 && istatus != 617 && istatus != 625 && istatus != 663 && istatus != 690){
						String mail = new String(strArray[2].trim());
						String country = new String(strArray[3].trim());
						String paidtime = new String(strArray[1].trim());
						String addtime = new String(strArray[5].trim());
						String sku = new String(strArray[7].trim());
						
						String strValue = new String();
						strValue.concat(country);
						strValue.concat("\001");
						strValue.concat(paidtime);
						strValue.concat("\001");
						strValue.concat(addtime);
						strValue.concat("\001");
						strValue.concat(sku);
						
						context.write(new Text(mail), new Text(strValue));					
					}
				}
			}
		}
	}

}

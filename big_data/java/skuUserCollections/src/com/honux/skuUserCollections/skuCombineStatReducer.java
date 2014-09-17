package com.honux.skuUserCollections;

import java.io.IOException;

import org.apache.hadoop.io.Text;
import org.apache.hadoop.io.LongWritable;
import org.apache.hadoop.mapreduce.Reducer;

public class skuCombineStatReducer extends Reducer<Text, Text, Text, LongWritable> {

	public void reduce(Text key, Iterable<Text> values, Context context)
			throws IOException, InterruptedException {
		int i =0;
		for(Text val:values){
			String str = val.toString();
			String[] strArray = str.split("\001");
			int value = Integer.parseInt(strArray[2].trim());
			
			i += value;
		}
		
		context.write(new Text(key), new LongWritable(i));
	}

}

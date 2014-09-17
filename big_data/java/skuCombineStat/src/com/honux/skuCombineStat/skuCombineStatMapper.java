package com.honux.skuCombineStat;

import java.io.IOException;

import org.apache.hadoop.io.LongWritable;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Mapper;

public class skuCombineStatMapper extends
		Mapper<LongWritable, Text, Text, Text> {

	public void map(LongWritable key, Text value, Context context)
			throws IOException, InterruptedException {
		String str = value.toString();
		String[] strArray = str.split("\001");
		String skuCombine = new String(strArray[0].trim());
		String skuCount = new String(strArray[2].trim());
		
		context.write(new Text(skuCombine), new Text(skuCount));
	}
}

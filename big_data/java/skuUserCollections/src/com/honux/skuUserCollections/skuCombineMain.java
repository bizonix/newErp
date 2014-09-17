package com.honux.skuUserCollections;

import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.fs.Path;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.io.LongWritable;
import org.apache.hadoop.mapreduce.Job;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;
import org.apache.hadoop.util.GenericOptionsParser;


public class skuCombineMain {

	public static void main(String[] args) throws Exception {
		Configuration conf1 = new Configuration();
		String[] argArray=new GenericOptionsParser(conf1, args).getRemainingArgs();  
		/*
        if(argArray.length!=4){  
            System.out.println("²ÎÊý´íÎó");  
            System.exit(1);  
        }  
        */
        
		Job job1 = Job.getInstance(conf1, "SkuCombineJob");
		job1.setJarByClass(com.honux.skuUserCollections.skuCombineMain.class);
		job1.setMapperClass(skuUserMapper.class);
		job1.setReducerClass(skuUserReducer.class);
		
		job1.setOutputKeyClass(Text.class);
		job1.setOutputValueClass(Text.class);

		//FileInputFormat.setInputPaths(job, new Path(argArray[0]));
		FileInputFormat.addInputPath(job1, new Path(argArray[0]));
		//FileInputFormat.addInputPath(job1, new Path(argArray[1]));
		FileOutputFormat.setOutputPath(job1, new Path(argArray[1]));	
		job1.waitForCompletion(true);
	}
}

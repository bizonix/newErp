package com.honux.skuCombineStat;

import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.fs.Path;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Job;
import org.apache.hadoop.mapreduce.Mapper;
import org.apache.hadoop.mapreduce.Reducer;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;
import org.apache.hadoop.util.GenericOptionsParser;

public class skuCombineStatMain {

	public static void main(String[] args) throws Exception {
		Configuration conf = new Configuration();
		String[] argArray=new GenericOptionsParser(conf, args).getRemainingArgs();  
		/*
        if(argArray.length!=4){  
            System.out.println("²ÎÊý´íÎó");  
            System.exit(1);  
        }  
        */
        
		Job job = Job.getInstance(conf, "SkuCombineStatJob");
		job.setJarByClass(com.honux.skuCombineStat.skuCombineStatMain.class);
		job.setMapperClass(skuCombineStatMapper.class);
		job.setReducerClass(skuCombineStatReducer.class);
		
		job.setOutputKeyClass(Text.class);
		job.setOutputValueClass(Text.class);

		//FileInputFormat.setInputPaths(job, new Path(argArray[0]));
		FileInputFormat.addInputPath(job, new Path(argArray[0]));
		//FileInputFormat.addInputPath(job1, new Path(argArray[1]));
		FileOutputFormat.setOutputPath(job, new Path(argArray[1]));	

		if (!job.waitForCompletion(true))
			return;
	}

}

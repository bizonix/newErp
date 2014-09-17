package com.honux.eob;

import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.fs.Path;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Job;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;
import org.apache.hadoop.util.GenericOptionsParser;

public class EbayOrderDetailMain {

	public static void main(String[] args) throws Exception {
		Configuration conf = new Configuration();
		String [] argArray=new GenericOptionsParser(conf, args).getRemainingArgs();  
		/*
        if(argArray.length!=4){  
            System.out.println("²ÎÊý´íÎó");  
            System.exit(1);  
        }  
        */
        
		Job job = Job.getInstance(conf, "EbayOrderDetailJob");
		job.setJarByClass(com.honux.eob.EbayOrderDetailMain.class);
		job.setMapperClass(EbayOrderDetailMapper.class);
		job.setReducerClass(EbayOrderDetailReducer.class);
		
		job.setOutputKeyClass(Text.class);
		job.setOutputValueClass(Text.class);

		System.out.print(argArray[0]+"\n");
		System.out.print(argArray[1]+"\n");
		System.out.print(argArray[2]+"\n");
		//FileInputFormat.setInputPaths(job, new Path(argArray[0]));
		FileInputFormat.addInputPath(job, new Path(argArray[0]));
		FileInputFormat.addInputPath(job, new Path(argArray[1]));
		FileOutputFormat.setOutputPath(job, new Path(argArray[2]));	

		if (!job.waitForCompletion(true))
			return;
	}

}

package com.honux.eob;

import java.io.IOException;
import java.util.*;

import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Reducer;

public class EbayOrderDetailReducer extends Reducer<Text, Text, Text, Text> {

	public void reduce(Text key, Iterable<Text> values, Context context)
			throws IOException, InterruptedException {
		//System.out.printf("Now in the Reducer print test 1  and the key %s\n",key);
		List<String> leftList=new ArrayList<String>();
		List<String> rightList=new ArrayList<String>();
		for (Text val : values) {
			String str = val.toString();
			//此处的“+”需要转义
			System.out.print(str+"\n");
			String[] strArray = str.split("\\+");
			if (strArray[0].equals("1")){
				leftList.add(strArray[1].toString());
				leftList.add(strArray[2].toString());
				leftList.add(strArray[3].toString());
				leftList.add(strArray[4].toString());
				leftList.add(strArray[5].toString());
				/*
				System.out.printf("Now in the Reducer print test 2\n");
				Iterator itl = leftList.iterator();
				while(itl.hasNext()){
					System.out.print(itl.next()+"\n");
				}
				*/
			}else if(strArray[0].equals("2")){
				try{
					rightList.add(strArray[1].toString());
					rightList.add(strArray[2].toString());
				}catch(java.lang.ArrayIndexOutOfBoundsException e){
					rightList.add(null);
				}
				/*
				System.out.printf("Now in the Reducer print test 3\n");
				Iterator itr = rightList.iterator();
				while(itr.hasNext()){
					System.out.print(itr.next()+"\n");
				}
				*/
			}else{
				//System.out.printf("%s\n", "the data is wrong in the Reducer!");
				continue;
			}
		}
		
		//System.out.print(leftList.size()+"in the reducer!\n");
		if (leftList.size() == 5){
			int r_size=rightList.size();
			//System.out.printf("Now in the Reducer print test 4 and %d\n",r_size);
			if (r_size%2==0){
				//System.out.printf("Now in the Reducer print test 5 and %d\n",r_size);
				for (int i = 0;i<r_size/2;i++){
					//System.out.printf("Now in the Reducer print test 6\n");
					context.write(new Text(key), new Text(leftList.get(0)+"\001"+leftList.get(1)+"\001"+leftList.get(2)+"\001"+leftList.get(3)+"\001"+leftList.get(4)+"\001"
							+rightList.get(i*2)+"\001"+rightList.get(i*2+1)));
				}
			}
		}
	}

}

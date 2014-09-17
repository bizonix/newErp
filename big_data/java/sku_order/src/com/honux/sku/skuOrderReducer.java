package com.honux.sku;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Reducer;

public class skuOrderReducer extends Reducer<Text, Text, Text, Text> {

	public void reduce(Text key, Iterable<Text> values, Context context)
			throws IOException, InterruptedException {
		List<String> leftList=new ArrayList<String>();
		List<String> rightList=new ArrayList<String>();
		for (Text val : values) {
			String str = val.toString();
			//此处的“+”需要转义
			System.out.print(str+"\n");
			String[] strArray = str.split("\\+");
			if (strArray[0].equals("1")){
				leftList.add(strArray[1].toString());
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
					rightList.add(strArray[3].toString());
					rightList.add(strArray[4].toString());
					rightList.add(strArray[5].toString());
					rightList.add(strArray[6].toString());
					rightList.add(strArray[7].toString());
					rightList.add(strArray[8].toString());
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
		if (rightList.size()>0){
			int r_size=rightList.size();
			//System.out.printf("Now in the Reducer print test 4 and %d\n",r_size);
			if (r_size%8==0){
				//System.out.printf("Now in the Reducer print test 5 and %d\n",r_size);
				for (int i = 0;i<r_size/8;i++){
					//System.out.printf("Now in the Reducer print test 6\n");
					String outValue = new String();
					outValue.concat(rightList.get(i*2));
					outValue.concat("\001");
					outValue.concat(rightList.get(i*2+1));
					outValue.concat("\001");
					outValue.concat(rightList.get(i*2+2));
					outValue.concat("\001");
					outValue.concat(rightList.get(i*2+3));
					outValue.concat("\001");
					outValue.concat(rightList.get(i*2+4));
					outValue.concat("\001");
					outValue.concat(rightList.get(i*2+5));
					outValue.concat("\001");
					outValue.concat(rightList.get(i*2+6));
					outValue.concat("\001");
					outValue.concat(rightList.get(i*2+7));
					outValue.concat("\001");
					outValue.concat(key.toString());
					outValue.concat("\001");
					outValue.concat(rightList.get(i*2+8));
					outValue.concat("\001");
					if(leftList.size()==1){
						outValue.concat(leftList.get(0));	
					}else if(leftList.size()==0){
						outValue.concat("null");
					}
					
					context.write(new Text(key), new Text(outValue));
				}
			}
		}
	}
}


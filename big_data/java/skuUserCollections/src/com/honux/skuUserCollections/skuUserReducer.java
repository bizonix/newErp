package com.honux.skuUserCollections;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Reducer;

public class skuUserReducer extends Reducer<Text, Text, Text, Text> {

	public void reduce(Text key, Iterable<Text> values, Context context)
			throws IOException, InterruptedException {
		List<String> skuCollections = new ArrayList<String>();
		String country = new String();
		String combineSku = new String();
		for (Text val : values) {
			String str = val.toString();
			String[] strArray = str.split("\001");
			country = strArray[1].trim();
			
			if(!skuCollections.contains(strArray[4].trim())){
				skuCollections.add(strArray[4].trim());
			}
		}
		
		if (skuCollections.size()>1){
			for(int i = 0;i<skuCollections.size()-1;i++){
				for(int j = i+1;j<skuCollections.size();j++){
					if(skuCollections.get(i) ==skuCollections.get(j)){
						continue;
					}
					
					if(skuCollections.get(i).length() < skuCollections.get(j).length()){
						combineSku.concat(skuCollections.get(i));
						combineSku.concat("+");
						combineSku.concat(skuCollections.get(j));
					}else if((skuCollections.get(i).length() < skuCollections.get(j).length()) && skuCollections.get(i).compareTo(skuCollections.get(j))<0){
						combineSku.concat(skuCollections.get(i));
						combineSku.concat("+");
						combineSku.concat(skuCollections.get(j));
					}else{
						combineSku.concat(skuCollections.get(j));
						combineSku.concat("+");
						combineSku.concat(skuCollections.get(i));
					}
					
					context.write(new Text(combineSku),new Text(country+"\001"+"1"));
				}
			}
		}
	}

}

<?php

class CategoryView extends BaseView{

	//页面渲染输出
	public function view_getCategoryList(){
		//调用action层， 获取列表数据
		$categoryAct  = new CategoryAct();
        $where = 'WHERE is_delete=0 ';
        $total = $categoryAct->act_getCategoryCount($where);
		$num      = 100;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= $page->limit;
		$categoryList = $categoryAct->act_getCategoryList('*',$where);
        //var_dump($categoryList);
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别信息列表'
			),
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 41);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign("categoryList",empty($categoryList)?null:$categoryList);
		$this->smarty->assign("title","类别列表");
        $this->smarty->display("categoryList.htm");
	}


	public function view_addCategory(){
		$categoryAct  = new CategoryAct();
		$categoryList = $categoryAct->act_getCategoryList('*','where is_delete=0 and pid=0');
        //print_r($CategoryList);
        $navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别信息列表'
			),
			array (
				'url' => 'index.php?mod=category&act=addCategory',
				'title' => '新增类别'
			),
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 41);
		$this->smarty->assign("title","新增分类");
        $this->smarty->assign("categoryList",$categoryList);
        $this->smarty->display("addCategory.htm");
	}

	//修改页面
	public function view_modCategory(){
		$id   		 = intval($_GET['id']);
		$categoryAct = new CategoryAct();
		$category    = $categoryAct->act_getCategoryList($select="*",$where="where id={$id}");


		$file = $category[0]['file'];
		if($file==1){
			$first_name  = $category[0]['name'];

			$f_CategoryAct = $categoryAct->act_getCategoryList($select="*",$where="where pid=0 and is_delete=0");
			$connect = '
				<tr>
				<td width="9%" align="right" bgcolor="#f2f2f2" class="left_txt">
				<div align="right"><span style="white-space: nowrap;">一级分类:</span></div></td>
				<td width="12%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="right">
						<select name="pid_one" id="pid_one" disabled="disabled" >';
			foreach($f_CategoryAct as $f){
				$connect .= '<option value="'.$f['id'].'"';
				if($f['name']==$first_name){$connect .= 'selected';}
				$connect .='>'.$f['name'].'</option>';
			}
			$connect .='';
			$connect .='</select>
					</div>
				</td>
				<td width="79%" align="right" bgcolor="#f2f2f2" class="left_txt">
				 <div align="left"><input value="'.$first_name.'" id="category_first" /></div>
				</td>
				</tr>
			';

			//$this->tp->set_var("first_name",$first_name);
		}else if($file==2){
			$second_name = $category[0]['name'];
			$second_pid  = $category[0]['pid'];
			$s_Category  = $categoryAct->act_getCategoryList($select="*",$where="where id={$second_pid}");
			$first_name  = $s_Category[0]['name'];

			$f_CategoryAct = $categoryAct->act_getCategoryList($select="*",$where="where pid=0 and is_delete=0");
			$s_CategoryAct = $categoryAct->act_getCategoryList($select="*",$where="where file=2 and is_delete=0");

			$connect = '
				<tr>
				<td width="9%" align="right" bgcolor="#f2f2f2" class="left_txt">
				<div align="right"><span style="white-space: nowrap;">一级分类:</span></div></td>
				<td width="12%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="right">
						<select name="pid_one" id="pid_one"  >';
			foreach($f_CategoryAct as $f){
				$connect .= '<option value="'.$f['id'].'"';
				if($f['name']==$first_name){$connect .= 'selected';}
				$connect .='>'.$f['name'].'</option>';
			}
			$connect .='</select>
					</div>
				</td>

			';
			$connect .='
				<td width="9%" align="right" bgcolor="#f2f2f2" class="left_txt">
				<div align="right"><span style="white-space: nowrap;">二级分类:</span></div></td>
				<td width="12%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="right" id="div_two">
					<select name="pid_two" id="pid_two" disabled="disabled">';
			foreach($s_CategoryAct as $s){
				$connect .= '<option value="'.$s['id'].'"';
				if($s['name']==$second_name){$connect .= 'selected';}
				$connect .='>'.$s['name'].'</option>';
			}
			$connect .='</select>
					</div>
				</td>
				<td width="79%" align="right" bgcolor="#f2f2f2" class="left_txt">
				<div align="left"><input value="'.$second_name.'" id="category_second" /></div></td>
			</tr>
			';
		}else if($file==3){
			$third_name  = $category[0]['name'];
			$third_pid   = $category[0]['pid'];
			$t_Category  = $categoryAct->act_getCategoryList($select="*",$where="where id={$third_pid}");
			$second_name = $t_Category[0]['name'];
			$second_pid  = $t_Category[0]['pid'];
			$s_Category  = $categoryAct->act_getCategoryList($select="*",$where="where id={$second_pid}");
			$first_name  = $s_Category[0]['name'];

			$f_CategoryAct = $categoryAct->act_getCategoryList($select="*",$where="where pid=0 and is_delete=0");
			$s_CategoryAct = $categoryAct->act_getCategoryList($select="*",$where="where file=2 and is_delete=0");
			$t_CategoryAct = $categoryAct->act_getCategoryList($select="*",$where="where file=3 and is_delete=0");

			$connect = '
				<tr>
				<td width="9%" align="right" bgcolor="#f2f2f2" class="left_txt">
				<div align="right"><span style="white-space: nowrap;">一级分类:</span></div></td>
				<td width="12%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="right">
						<select name="pid_one" id="pid_one" onchange="change_one();" >';
			foreach($f_CategoryAct as $f){
				$connect .= '<option value="'.$f['id'].'"';
				if($f['name']==$first_name){$connect .= 'selected';}
				$connect .='>'.$f['name'].'</option>';
			}
			$connect .='</select>
					</div>
				</td>

			';
			$connect .='
				<td width="9%" align="right" bgcolor="#f2f2f2" class="left_txt">
				<div align="right"><span style="white-space: nowrap;">二级分类:</span></div></td>
				<td width="12%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="right" id="div_two">
					<select name="pid_two" id="pid_two" >';
			foreach($s_CategoryAct as $s){
				$connect .= '<option value="'.$s['id'].'"';
				if($s['name']==$second_name){$connect .= 'selected';}
				$connect .='>'.$s['name'].'</option>';
			}
			$connect .='</select>
					</div>
				</td>
			';
			$connect .= '
				<td width="9%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="right"><span style="white-space: nowrap;">三级分类:</span></div></td>
			        <td width="12%" align="right" bgcolor="#f2f2f2" class="left_txt">
						<div align="right" id="div_three">
						<select name="pid_three" id="pid_three" onchange="change(3)" disabled="disabled">';
             foreach($t_CategoryAct as $t){
				$connect .= '<option value="'.$t['id'].'"';
				if($t['name']==$third_name){$connect .= 'selected';}
				$connect .='>'.$t['name'].'</option>';
			}
			$connect .='</select>
						</div>
					</td>
			        <td width="79%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="left"><input value="'.$third_name.'" id="category_third" /></div></td>
			</tr>';
		}else if($file==4){
			$four_name   = $category[0]['name'];
			$four_pid    = $category[0]['pid'];
			$f_Category  = $categoryAct->act_getCategoryList($select="*",$where="where id={$four_pid}");
			$third_name  = $f_Category[0]['name'];
			$third_pid   = $f_Category[0]['pid'];
			$t_Category  = $categoryAct->act_getCategoryList($select="*",$where="where id={$third_pid}");
			$second_name = $t_Category[0]['name'];
			$second_pid  = $t_Category[0]['pid'];
			$s_Category  = $categoryAct->act_getCategoryList($select="*",$where="where id={$second_pid}");
			$first_name  = $s_Category[0]['name'];

			$f_CategoryAct  = $categoryAct->act_getCategoryList($select="*",$where="where pid=0 and is_delete=0");
			$s_CategoryAct  = $categoryAct->act_getCategoryList($select="*",$where="where file=2 and is_delete=0");
			$t_CategoryAct  = $categoryAct->act_getCategoryList($select="*",$where="where file=3 and is_delete=0");
			$fo_CategoryAct = $categoryAct->act_getCategoryList($select="*",$where="where file=4 and is_delete=0");

			$connect = '
				<tr>
				<td width="9%" align="right" bgcolor="#f2f2f2" class="left_txt">
				<div align="right"><span style="white-space: nowrap;">一级分类:</span></div></td>
				<td width="12%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="right">
						<select name="pid_one" id="pid_one" onchange="change_one2();" >';
			foreach($f_CategoryAct as $f){
				$connect .= '<option value="'.$f['id'].'"';
				if($f['name']==$first_name){$connect .= 'selected';}
				$connect .='>'.$f['name'].'</option>';
			}
			$connect .='</select>
					</div>
				</td>

			';
			$connect .='
				<td width="9%" align="right" bgcolor="#f2f2f2" class="left_txt">
				<div align="right"><span style="white-space: nowrap;">二级分类:</span></div></td>
				<td width="12%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="right" id="div_two">
					<select name="pid_two" id="pid_two" onchange="change_two();" >';
			foreach($s_CategoryAct as $s){
				$connect .= '<option value="'.$s['id'].'"';
				if($s['name']==$second_name){$connect .= 'selected';}
				$connect .='>'.$s['name'].'</option>';
			}
			$connect .='</select>
					</div>
				</td>
			';
			$connect .= '
				<td width="9%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="right"><span style="white-space: nowrap;">三级分类:</span></div></td>
			        <td width="12%" align="right" bgcolor="#f2f2f2" class="left_txt">
						<div align="right" id="div_three">
						<select name="pid_three" id="pid_three"  >';
             foreach($t_CategoryAct as $t){
				$connect .= '<option value="'.$t['id'].'"';
				if($t['name']==$third_name){$connect .= 'selected';}
				$connect .='>'.$t['name'].'</option>';
			}
			$connect .='</select>
						</div>
					</td>
			';
			$connect .='
				<td width="9%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="right"><span style="white-space: nowrap;">四级分类:</span></div></td>
			        <td width="12%" align="right" bgcolor="#f2f2f2" class="left_txt">
						<div align="right" id="div_four">
						<select name="pid_four" id="pid_four" disabled="disabled">';
            foreach($fo_CategoryAct as $fo){
				$connect .= '<option value="'.$fo['id'].'"';
				if($fo['name']==$four_name){$connect .= 'selected';}
				$connect .='>'.$fo['name'].'</option>';
			}
			$connect .='</select>
						</div>
					</td>
			        <td width="79%" align="right" bgcolor="#f2f2f2" class="left_txt">
					<div align="left"><input value="'.$four_name.'" id="category_fourth" /></div></td>
			</tr>';

		}
		$navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别信息列表'
			),
			array (
				'url' => "index.php?mod=category&act=modCategory&id=$id",
				'title' => '修改类别'
			),
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 41);
		$this->smarty->assign("categoryid",$id);
		$this->smarty->assign("categoryfile",$file);
		$this->smarty->assign("connect",$connect);
		$this->smarty->assign("title","修改类别");
        $this->smarty->display("editCategory.htm");
	}


}
?>
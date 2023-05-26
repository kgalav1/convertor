<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 
*/
class html {
	public function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->model("Api_Model");
		
	}
	public $htmlDataListStyle=array();

		
    public function tableList($dataList,$recCount,$recToShow,$pageNo,$columns,$id,$edit=0,$delete=0,$onclickEdit='',$report="",$report_type=""){
    	if($report!='' and $report_type!='')
		{
			$SaleAmountSum=0;
			$ReceivedAmountSum=0;
			if($report=='order' and $report_type=='Buyer Wise')
			{
				$last_buyer='';
				$totalInvoicingPrice=0;
				$totalQuantity=0;
				$totalOrderPrice=0;

			}
			elseif($report=='order' and $report_type=='Grade Wise')
			{
				$last_item='';
				$totalInvoicingPrice=0;
				$totalQuantity=0;
				$totalOrderPrice=0;

			}
			elseif($report=='order' and $report_type=='Indenter Wise')
			{
				$last_indenter='';
				$totalInvoicingPrice=0;
				$totalQuantity=0;
				$totalOrderPrice=0;

			}
		}
    	$colSpan = count($columns) + 2;
        if(!empty($dataList) && count($dataList)>0){
			$i=1; 
			foreach($dataList as $data){
				$temp_onclick=$onclickEdit;
				//print_r($data);
				foreach ($data as $key2=>$value2) {
					$label_name='##'.$key2.'##';
					$temp_onclick=str_replace($label_name,$value2,$temp_onclick);
				}
				if($report!='' and $report_type!='')
				{
					if(($report=='order' || $report=='sale') and $report_type=='Buyer Wise')
					{

						$tagVal= $linkValBefore = $linkValAfter ='';
					    if(count($this-> htmlDataListStyle)>0 && isset($this-> htmlDataListStyle["buyer"]) && count($this-> htmlDataListStyle["buyer"])>0) {
			                if(isset($this-> htmlDataListStyle["buyer"]['tagString'])){
			                $tagVal = $this-> htmlDataListStyle["buyer"]['tagString']['tagVal'];
								if(isset($this-> htmlDataListStyle["buyer"]['tagString']['replaceColumn'])){
									foreach ($this-> htmlDataListStyle["buyer"]['tagString']['replaceColumn'] as $tagArray) {
										$tagVal=str_replace("##$tagArray##", $data->$tagArray, $tagVal);
									}
								}
							}
						}
						if(isset($this-> htmlDataListStyle["buyer"]['linkString'])){
		                $linkValBefore = $this-> htmlDataListStyle["buyer"]['linkString']['tagVal'];
							if(isset($this-> htmlDataListStyle["buyer"]['linkString']['replaceColumn'])){
								foreach ($this-> htmlDataListStyle["buyer"]['linkString']['replaceColumn'] as $tagArray) {
									$linkValBefore=str_replace("##$tagArray##", $data->$tagArray, $linkValBefore);
								}
							}
							$linkValAfter=$this-> htmlDataListStyle["buyer"]['linkString']['endString'];
						}
						if($data->buyer!=$last_buyer or $last_buyer=='')
						{

							
							$totalInvoicingPrice=0;
							$totalQuantity=0;
							$totalOrderPrice=0;
							$InvoicingPriceTotalSum=0;
							$netWeightSum=0;
							$SaleAmountSum=0;
							$ReceivedAmountSum=0;
						?>
						<tr>
							<td style="background:#e8eded;color:#000; font-weight:bold;" colspan="<?php echo count($columns)+1 ?>" <?php echo $tagVal; ?>><?php echo $linkValBefore; echo $data->buyer; echo $linkValAfter; ?></td>
						</tr>
						<?php
						}

						$last_buyer=$data->buyer;
						$totalInvoicingPrice+=$data->InvoicingPrice;
						$totalQuantity+=$data->QuantityOrdered;
						$totalOrderPrice+=$data->QuantityOrdered*$data->InvoicingPrice;
						$InvoicingPriceTotalSum+=$data->InvoicingPriceTotal;
						$netWeightSum+=$data->net_weight;
						$SaleAmountSum+=$data->InvoicingPrice*($data->net_weight/1000);
						$ReceivedAmountSum+=$data->received_amount;
					}
					if($report=='order' and $report_type=='Grade Wise')
					{

						$tagVal= $linkValBefore = $linkValAfter ='';
					    if(count($this-> htmlDataListStyle)>0 && isset($this-> htmlDataListStyle["buyer"]) && count($this-> htmlDataListStyle["buyer"])>0) {
			                if(isset($this-> htmlDataListStyle["buyer"]['tagString'])){
			                $tagVal = $this-> htmlDataListStyle["buyer"]['tagString']['tagVal'];
								if(isset($this-> htmlDataListStyle["buyer"]['tagString']['replaceColumn'])){
									foreach ($this-> htmlDataListStyle["buyer"]['tagString']['replaceColumn'] as $tagArray) {
										$tagVal=str_replace("##$tagArray##", $data->$tagArray, $tagVal);
									}
								}
							}
						}
						if(isset($this-> htmlDataListStyle["buyer"]['linkString'])){
		                $linkValBefore = $this-> htmlDataListStyle["buyer"]['linkString']['tagVal'];
							if(isset($this-> htmlDataListStyle["buyer"]['linkString']['replaceColumn'])){
								foreach ($this-> htmlDataListStyle["buyer"]['linkString']['replaceColumn'] as $tagArray) {
									$linkValBefore=str_replace("##$tagArray##", $data->$tagArray, $linkValBefore);
								}
							}
							$linkValAfter=$this-> htmlDataListStyle["buyer"]['linkString']['endString'];
						}
						if($data->grade_name!=$last_item or $last_item=='')
						{

							if($last_item!='')
							{
								
							}
							$totalContainersOrdered=0;
							$totalQuantity=0;
							$totalOrderPrice=0;
							$InvoicingPriceTotalSum=0;
							$netWeightSum=0;
							$SaleAmountSum=0;
							$ReceivedAmountSum=0;
						?>
						<tr>
							<td style="background:#e8eded;color:#000; font-weight:bold;" colspan="<?php echo count($columns)+1 ?>" <?php echo $tagVal; ?>><?php echo $linkValBefore; echo $data->grade_name; echo $linkValAfter; ?></td>
						</tr>
						<?php
						}

						$last_item=$data->grade_name;
						$totalContainersOrdered+=$data->ContainersOrdered;
						$totalQuantity+=$data->QuantityOrdered;
						$totalOrderPrice+=$data->QuantityOrdered*$data->InvoicingPrice;
						$InvoicingPriceTotalSum+=$InvoicingPriceTotal;
						$netWeightSum+=$data->net_weight;
						$SaleAmountSum+=$data->InvoicingPrice*($data->net_weight/1000);
						$ReceivedAmountSum+=$data->received_amount;
					}

					if($report=='order' and $report_type=='Indenter Wise')
					{

						$tagVal= $linkValBefore = $linkValAfter ='';
					    if(count($this-> htmlDataListStyle)>0 && isset($this-> htmlDataListStyle["buyer"]) && count($this-> htmlDataListStyle["buyer"])>0) {
			                if(isset($this-> htmlDataListStyle["buyer"]['tagString'])){
			                $tagVal = $this-> htmlDataListStyle["buyer"]['tagString']['tagVal'];
								if(isset($this-> htmlDataListStyle["buyer"]['tagString']['replaceColumn'])){
									foreach ($this-> htmlDataListStyle["buyer"]['tagString']['replaceColumn'] as $tagArray) {
										$tagVal=str_replace("##$tagArray##", $data->$tagArray, $tagVal);
									}
								}
							}
						}
						if(isset($this-> htmlDataListStyle["buyer"]['linkString'])){
		                $linkValBefore = $this-> htmlDataListStyle["buyer"]['linkString']['tagVal'];
							if(isset($this-> htmlDataListStyle["buyer"]['linkString']['replaceColumn'])){
								foreach ($this-> htmlDataListStyle["buyer"]['linkString']['replaceColumn'] as $tagArray) {
									$linkValBefore=str_replace("##$tagArray##", $data->$tagArray, $linkValBefore);
								}
							}
							$linkValAfter=$this-> htmlDataListStyle["buyer"]['linkString']['endString'];
						}
						if($data->indenter_name!=$last_item or $last_item=='')
						{

							if($last_item!='')
							{
								
							}
							$totalContainersOrdered=0;
							$totalQuantity=0;
							$totalOrderPrice=0;
							$InvoicingPriceTotalSum=0;
							$netWeightSum=0;
							$SaleAmountSum=0;
							$ReceivedAmountSum=0;
						?>
						<tr>
							<td style="background:#e8eded;color:#000; font-weight:bold;" colspan="<?php echo count($columns)+1 ?>" <?php echo $tagVal; ?>><?php echo $linkValBefore; echo $data->indenter_name; echo $linkValAfter; ?></td>
						</tr>
						<?php
						}

						$last_item=$data->indenter_name;
						$totalContainersOrdered+=$data->ContainersOrdered;
						$totalQuantity+=$data->QuantityOrdered;
						$totalOrderPrice+=$data->QuantityOrdered*$data->InvoicingPrice;
						$InvoicingPriceTotalSum+=$InvoicingPriceTotal;
						$netWeightSum+=$data->net_weight;
						$SaleAmountSum+=$data->InvoicingPrice*($data->net_weight/1000);
						$ReceivedAmountSum+=$data->received_amount;
					}
				}

			 ?>
				<tr>
				<?php
				if((!isset($data->sale_id) and $this->CI->uri->segment(1)!='orderreport' and $this->CI->uri->segment(1)!='order' and $this->CI->uri->segment(1)!='commissionreport') OR isset($data->payment_id))
				{
				?>
				<td style="width:50px;"><?php echo $i+(($pageNo-1)*$recToShow); ?></td>
                <?php 
            	}
                foreach($columns as $column){
					//Add Link, Style ,Button,Div etc ****************************** Starts Here
                		 // array('column_name'=>array('linkString' => array('tagVal' => "<span style='color:#337ab7;cursor:pointer' onclick=\"getEditRec('##comp_id##');\">", 'replaceColumn' => array('comp_id'),'endString'=>'</span>'), 'tagString' => array('tagVal' => "style='color:#337ab7;cursor:pointer' onclick=\"getEditRec('##comp_id##');\" ", 'replaceColumn' => array('comp_id'))
						 // ));
						 //for one css text align right side
						 // 'column_name'=>array('tagString' => array('tagVal' => "align='right'"))
                		 
                	//New Code Starts Here
	                $tagVal= $linkValBefore = $linkValAfter ='';
				    if(count($this-> htmlDataListStyle)>0 && isset($this-> htmlDataListStyle["$column"]) && count($this-> htmlDataListStyle["$column"])>0) {
	                if(isset($this-> htmlDataListStyle["$column"]['tagString'])){
	                $tagVal = $this-> htmlDataListStyle["$column"]['tagString']['tagVal'];
						if(isset($this-> htmlDataListStyle["$column"]['tagString']['replaceColumn'])){
							foreach ($this-> htmlDataListStyle["$column"]['tagString']['replaceColumn'] as $tagArray) {
								$tagVal=str_replace("##$tagArray##", $data->$tagArray, $tagVal);
							}
						}
					}
					if(isset($this-> htmlDataListStyle["$column"]['linkString'])){
	                $linkValBefore = $this-> htmlDataListStyle["$column"]['linkString']['tagVal'];
						if(isset($this-> htmlDataListStyle["$column"]['linkString']['replaceColumn'])){
							foreach ($this-> htmlDataListStyle["$column"]['linkString']['replaceColumn'] as $tagArray) {
								$linkValBefore=str_replace("##$tagArray##", $data->$tagArray, $linkValBefore);
							}
						}
						$linkValAfter=$this-> htmlDataListStyle["$column"]['linkString']['endString'];
					}
                }	
      			// New Code Starts Here	
			//Add Link, Style ,Button,Div etc ****************************** Ends Here
         		if($column=='balance_containers')
         		{
         			?>
         			<td style="text-align: right;<?php if($data->$column<=0 || ($data->indent_close_date!='' && $data->indent_close_date!='0000-00-00' && $data->indent_close_date!='1970-01-01')){ echo 'border:4px solid;border-color:#5cb85c;';  } else { echo 'border:4px solid;border-color:#d9534f;'; } ?>" > <?php echo $linkValBefore; if($column==$id){ echo '#'; } 
         		}	
         		else
         		{
				?>
                <td 
               	<?php echo $tagVal; ?> > <?php echo $linkValBefore; if($column==$id){ echo '#'; } 
           		}
               if($column=='OrderInvoicetotal')
               	{ 
               		echo number_format((float)$data->InvoicingPrice*$data->QuantityOrdered, 2, '.', ''); 
               	}
               	elseif($column=='jobs')
               	{ 
               		$explode=explode('<<>>',$data->$column);
					$jobs=explode(',',$explode[0]);
					$no_of_containers=explode(',',$explode[1]);
					for ($i=0; $i <count($jobs) ; $i++) 
					{ 
						echo $jobs[$i].' <b>X</b> '.$no_of_containers[$i].'<br>';
					}
					if($data->indent_close_date!='' && $data->indent_close_date!='0000-00-00' && $data->indent_close_date!='1970-01-01' && $data->balance_containers>0)
					{
						echo "<b>Closed</b>";
					}
               	} 
               	elseif($column=='balance_containers')
               	{ 
               		echo str_replace('-','+',$data->$column);
               	} 
               	elseif($column=='invoice_amount')
               	{ 
               		echo number_format((float)$data->InvoicingPrice*($data->net_weight/1000), 2, '.', ''); 
               	} 
               	else
               		{ echo $data->$column; } 
               	echo $linkValAfter; ?>
                	
                </td>
                <?php } ?>
                <?php if($id!=NULL){ ?>
                    <td style="white-space:nowrap; width:80px;">
                    <?php if($edit==1)
                    { 
                    	if(isset($dataList[0]->booking_id))
                    	{
                    		?>
                    		<a title="Set Time Frame" target="_blank" class="btn btn-warning btn-xs" href="<?php echo base_url() ?>timeframe?booking_reference=<?php echo str_replace('/','-',$data->booking_reference) ?>"><i class="fa fa-clock-o" aria-hidden="true"></i></a>
                    		<a title="Upload Documents" target="_blank" class="btn btn-success btn-xs" href="<?php echo base_url() ?>uploaddocuments?booking_reference=<?php echo str_replace('/','-',$data->booking_reference) ?>"><i class="fa fa-upload" aria-hidden="true"></i></a>
                    		<a href="javascript:void(0)" class="btn btn-primary btn-xs" onClick="<?php echo $temp_onclick; ?>;getEditRecBooking('<?php echo $data->$id; ?>');"><i class="fa fa-pen" aria-hidden="true"></i></a> 
                    		<?php
                    	}
                    	else
                    	{
                    		if(isset($dataList[0]->sale_id) and !isset($dataList[0]->payment_id))
                    		{
                    			?>
                    			<a title="Payment Info" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal<?php echo $data->$id ?>">
				                    <i class="fa fa-dollar icon-white"></i>
				                </a>
				                <?php
				                $paymentInfo['sale_id']=$data->$id;
				                $paymentInfo['invoice_no']=$data->invoice_no_display;
				                $this->CI->load->model('Payment_Model');
				                $where=" t1.invoice_no='".$paymentInfo['invoice_no']."' ";
				                $paymentInfo['payments']=$this->CI->Payment_Model->dataList($where);
				                echo $this->CI->load->view("sale/paymentinfo",$paymentInfo,true);
				                if($data->IsDebitNote!=1)
				                {
                    			?>
                    			<a title="Download Documents" target="_blank" class="btn btn-success btn-xs" href="<?php echo base_url() ?>documents?invoice_no=<?php echo str_replace('/','-',$data->invoice_no_display) ?>&buyer=<?php echo $data->buyer_id ?>"><i class="fa fa-download" aria-hidden="true"></i></a> 	
                    			<?php
                    			}
                    		}
	                    	?>
	                        <a href="javascript:void(0)" class="btn btn-primary btn-xs" onClick="<?php echo $temp_onclick; ?>;getEditRec('<?php echo $data->$id; ?>');"><i class="fa fa-pen" aria-hidden="true"></i></a> 
	                    <?php 
	                	}
                	}
                    else{ ?>
                        <a href="javascript:void(0)" class="btn btn-primary btn-xs disabled"><i class="fa fa-pen" aria-hidden="true"></i></a> 
                    <?php } ?>
                    <?php if($delete==1){ ?>
                        <a href="javascript:void(0)" class="btn btn-danger btn-xs" onClick="deleteRec('<?php echo $data->$id; ?>')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    <?php }else{ ?>
                        <a href="javascript:void(0)" class="btn btn-danger btn-xs disabled"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    <?php } ?>
                    </td>
                <?php } ?>
			  </tr>      
		  <?php 
		  if(count($dataList)==$i and $report=='order' and $report_type=='Buyer Wise')
			{
				
				
			}
			if(count($dataList)==$i and $report=='order' and $report_type=='Grade Wise')
			{
					
				
				
			}
			$last_currency=$data->InvoicingCurrency;
		  $i++; } ?>
		  <tr>
		  <td colspan="<?php echo $colSpan; ?>" style="background:#fff">
		  <?php $this->pagination($recCount,$recToShow,$pageNo); ?>
		  </td>
		  </tr>
    <?php } else { ?>
        <tr><td style="text-align:center;" colspan="<?php echo $colSpan; ?>">No Records Found</td></tr>
    <?php }
	}
	
	public function pagination($recCount,$recToShow,$pageNo,$pageShow=5,$type='AJAX'){
		$totalPage = ceil($recCount/$recToShow);
		if($pageNo<=$pageShow){
		  $stPage=1;
		}else{
		  $stPage = $pageNo-$pageShow;
		}
		$endPage = $pageNo+$pageShow;
		if($endPage>=$totalPage){
		  $endPage = $totalPage;
		}
		?>
		
		<ul class="pagination pagination-sm pull-left" style="margin:0; display:block;" data-page="<?php echo $totalPage; ?>">
			<?php if($pageNo!=1){ ?>
            <li onClick="pagination('F','<?php echo $type; ?>',this)"><a href="javascript:void(0)">First</a></li>
            <li onClick="pagination('P','<?php echo $type; ?>',this)"><a href="javascript:void(0)">Previous</a></li>
            <?php } ?>
            <?php for ($i = $stPage; $i <= $endPage; $i++) { ?>
            <li <?php if($pageNo==$i) echo 'class="active"'; ?> onClick="pagination('<?php echo $i; ?>','<?php echo $type; ?>',this)"><a href="javascript:void(0)"><?php echo $i; ?></a></li>
            <?php } ?>
            <?php if($pageNo!=$totalPage){ ?>
            <li onClick="pagination('N','<?php echo $type; ?>',this)"><a href="javascript:void(0)">Next</a></li>
            <li onClick="pagination('L','<?php echo $type; ?>',this)"><a href="javascript:void(0)">Last</a></li>
            <?php } ?>
		</ul>
		
		<p class="pull-right" style="line-height:30px; margin:0; padding:0 10px;"><strong>Total Pages:</strong> <?php echo $totalPage; ?><strong style="padding-left:15px;">Total Records:</strong> <?php echo $recCount; ?></p>
		<?php
	}	
	
	public function optionGen($dataList,$selected=''){
		foreach($dataList as $key=>$val){ ?>
			<option value="<?php echo $key; ?>" <?php if($key == $selected){ echo ' selected'; } ?>><?php  echo $val; ?></option>
	<?php
		 }
	}
	
	public function selectList($dataList,$value,$option,$selected=''){
		foreach($dataList as $data){ ?>
			<option value="<?php echo $data->$value; ?>" <?php if($data->$value == $selected){ echo ' selected'; } ?>><?php  echo $data->$option; ?></option>
	<?php
		 }
	}
	public function getMetaDropdown($metaType,$selected=NULL,$where=NULL){
		if($where != NULL) $where = 'meta_type="'.$metaType.'" and '.$where; else $where = 'meta_type="'.$metaType.'"';
		foreach($this->CI->Acc_Model->getMetaTypeList($where) as $dropdown){ ?>
		<option value="<?php echo $dropdown->meta_id; ?>" <?php if($dropdown->meta_id==$selected) echo ' selected'; ?>><?php echo $dropdown->meta_name; ?></option>
        <?php }
	}
    
	public function getStateDropdown($selected=NULL){
		foreach($this->CI->Acc_Model->getStateList() as $dropdown){ ?>
		<option value="<?php echo $dropdown->state_code; ?>" <?php if($dropdown->state_code==$selected) echo ' selected'; ?>><?php echo $dropdown->state_name; ?></option>
        <?php }
	}
            
	public function getClientDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = " ((1=1 and parent_id=".$this->CI->fx->clientAccessID.") or client_id!=".$this->CI->fx->clientId.")";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getClientList($whereSt) as $dropdown){ ?>
		<option value="<?php echo $dropdown->client_id; ?>" <?php if($dropdown->client_id==$selected) echo ' selected'; ?>><?php echo $dropdown->clientName; ?></option>
        <?php }
	}
	  
	public function getCompanyDropdown($status=NULL,$selected=NULL,$where=NULL){
		//$whereSt = ($this->CI->fx->masterClient==0)? "1=1 and client_id=".$this->CI->fx->clientId:"1=1 and sub_client_id=".$this->CI->fx->clientId;
		$whereSt = ($this->CI->fx->masterClient==0)? "1=1 and client_id=".$this->CI->fx->clientId:"1=1 and client_id=".$this->CI->fx->clientId;
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		$compList = $this->CI->Acc_Model->getCompanyList($whereSt);
		if(count($compList)>0){
		foreach($compList as $dropdown){ ?>
		<option value="<?php echo $dropdown->comp_id; ?>" <?php if($dropdown->comp_id==$selected) echo ' selected'; ?>><?php echo $dropdown->name; ?></option>
        <?php }
		}else{ ?>
        <option value="">No Company Assigned to you</option>
        <?php
		}
	}
	
	public function getBranchDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		
		$subUserBrnch = array();

		if($this->CI->fx->masterClient!=0){
			$subUserBrnch = array('sub_client_id'=>$this->CI->fx->clientId,'comp_id'=>$this->CI->fx->clientCompId);
		}
		
		foreach($this->CI->Acc_Model->getBranchList($whereSt,$this->CI->fx->clientCompDb,$subUserBrnch) as $dropdown){ ?>
		<option value="<?php echo $dropdown->branch_id; ?>" <?php if($dropdown->branch_id==$selected) echo ' selected'; ?>><?php echo $dropdown->branch_name; ?></option>
        <?php }
	}
	
	public function getInvoiceTypeDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getInvoiceTypeList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->invoicetype_id; ?>" <?php if($dropdown->invoicetype_id==$selected) echo ' selected'; ?>><?php echo $dropdown->invoice_type; ?></option>
        <?php }
	}
	  
	public function getFinyrDropdown($db=NULL,$selected=NULL,$where=NULL){
		$db = ($db!=NULL)? $db:$this->CI->fx->clientCompDb; 
		$whereSt = "1=1";
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getFinyrList($whereSt,$db) as $dropdown){ ?>
		<option value="<?php echo $dropdown->finyr_id; ?>" <?php if($dropdown->finyr_id==$selected) echo ' selected'; ?>><?php echo $dropdown->finyr_name; ?></option>
        <?php }
	}
	
	public function getTaxDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getTaxList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->tax_id; ?>" <?php if($dropdown->tax_id==$selected) echo ' selected'; ?>><?php echo $dropdown->tax_name; ?></option>
        <?php }
	}
	public function getItemCateDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getItemCateList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->itemcat_id; ?>" <?php if($dropdown->itemcat_id==$selected) echo ' selected'; ?>><?php echo $dropdown->name; ?></option>
        <?php }
	}
	  
	public function getItemMakeDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getItemMakeList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->make_id; ?>" <?php if($dropdown->make_id==$selected) echo ' selected'; ?>><?php echo $dropdown->make_name; ?></option>
        <?php }
	}
	  
	public function getItemDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getItemList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->item_id; ?>" <?php if($dropdown->item_id==$selected) echo ' selected'; ?>><?php echo $dropdown->item_name; ?></option>
        <?php }
	}
	  
	public function getGroupDropdown($selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getGroupList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->group_id; ?>" <?php if($dropdown->group_id==$selected) echo ' selected'; ?>><?php echo $dropdown->group_name; ?></option>
        <?php }
	}
	
	public function getSubGroupDropdown($selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getSubGroupList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->sub_group_id; ?>" data-type="<?php echo $this ->CI -> fx ->getDebitCreditByBehaiour($dropdown->behaviour); ?>" data-address="<?php echo $dropdown->accept_address; ?>" <?php if($dropdown->sub_group_id==$selected) echo ' selected'; ?>><?php echo $dropdown->sub_group_name; ?></option>
        <?php }
	}
	public function getSubGroupDropdownWithDetail($selected=NULL,$where=array(),$behaviourOr=array()){
		$whereSt = "1=1";
		foreach($this->CI->Acc_Model->getSubGroupListWithDetail($where,$behaviourOr,$this->CI->fx->clientCompDb) as $dropdown){ ?>
			<option data-behaviour="<?php echo $dropdown->behaviour; ?>" value="<?php echo $dropdown->sub_group_id; ?>" data-address="<?php echo $dropdown->accept_address; ?>" <?php if($dropdown->sub_group_id==$selected) echo ' selected'; ?>><?php echo $dropdown->sub_group_name; ?></option>
        <?php }
	}
	public function getLedgerDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getLedgerList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->ledger_id; ?>" <?php if($dropdown->ledger_id==$selected) echo ' selected'; ?>><?php echo $dropdown->acc_head; ?></option>
        <?php }
	}
	
	public function getAgentDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getAgentList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->agent_id; ?>" <?php if($dropdown->agent_id==$selected) echo ' selected'; ?>><?php echo $dropdown->agent_name; ?></option>
        <?php }
	}
	
	public function getChallanTypeDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getChallanTypeList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->ch_type_id; ?>" <?php if($dropdown->ch_type_id==$selected) echo ' selected'; ?>><?php echo $dropdown->ch_type; ?></option>
        <?php }
	}
	
	public function getCostCenterDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getCostCenterList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->cc_id; ?>" <?php if($dropdown->cc_id==$selected) echo ' selected'; ?>><?php echo $dropdown->cc_name.' #'.$dropdown->cc_unique_id; ?></option>
        <?php }
	}
	
	public function getLedgerAddressDropdown($selectedVal='',$ledger_id){
		$this->CI->load->model("Getdata_Model");
		$dataArray = $this ->CI-> Getdata_Model -> getLedgerMultipleAddress(array('ledger_id' => $ledger_id), array('ledger_master_id' => $ledger_id));
		$htmlString = "<option value=''>Select Address</option>";
		$data = '';
		if (count($dataArray) > 0) {
			foreach ($dataArray as $key => $value) {
				$address = $value['address1'] . ', ';
				if ($value['address1'] != '')
					$address .= $value['address2'] . ', ';
				$address .= $value['city'] . ', ' . $value['state'] . ', ' . $value['pincode'] . ', ' . $value['country'];
				$selected="";
				if ($value['id'] == $selectedVal){
					$selected="selected=''";
				}				
				$htmlString .= "<option $selected value='" . $value['id'] . "'data-address='" . $address . "'>" . $value['branch_name'] . "-" . $value['pincode'] . "</option>";
			}
		} else {
			$htmlString = "<option value=''>No Address Found</option>";
		}
		echo $htmlString;
	}
	public function getMessageTemplaeDropdown($selected=NULL,$queryArray=array()){
		$this->CI->load->model("Template_Model");
	 	foreach($this->CI->Template_Model->getTemplateList($queryArray,array()) as $dropdown){ ?>
		<option data-temp_type="<?php echo $dropdown['temp_type']; ?>" value="<?php echo $dropdown['id']; ?>" <?php if($dropdown['id']==$selected) echo ' selected'; ?>><?php echo $dropdown['temp_type'].'- '.$dropdown['template_name']; ?></option>
        <?php }
	 }
	
	public function getLeadTypeDropdown($selected=NULL,$queryArray=array()){
	 	foreach($this->CI->Acc_Model->getLeadTypeList($queryArray,array()) as $dropdown){ ?>
		<option value="<?php echo $dropdown['id']; ?>" <?php if($dropdown['id']==$selected) echo ' selected'; ?>><?php echo $dropdown['lead_type_name']; ?></option>
        <?php }
	 }

	public function getLeadSourceDropdown($selected=NULL,$queryArray=array()){
		 	foreach($this->CI->Acc_Model->getLeadSourceList($queryArray,array()) as $dropdown){ ?>
			<option value="<?php echo $dropdown['id']; ?>" <?php if($dropdown['id']==$selected) echo ' selected'; ?>><?php echo $dropdown['lead_source_name']; ?></option>
	        <?php }
	}
	public function getLeadStatusDropdown($selected=NULL,$queryArray=array()){
	 	foreach($this->CI->Acc_Model->getLeadStatusList($queryArray,array()) as $dropdown){ ?>
		<option style="color: <?php echo @$dropdown['color_code'] ?>" value="<?php echo $dropdown['id']; ?>" <?php if($dropdown['id']==$selected) echo ' selected'; ?>><?php echo $dropdown['lead_status_name']; ?></option>
        <?php }
	 }
	public function getHourDropDown($selected=""){
		  for ($i=0; $i <24 ; $i++) {
		 		$hour=sprintf("%02d",$i); 
		?>
       	<option <?php if($selected==$hour){ echo 'selected'; } ?> value="<?php echo $hour; ?>"><?php echo $hour; ?></option>
       	<?php } 
	}
	
	function contractReminderDaysDropdown($selected="",$detault=0){
		$selectedArray=array();
		if($selected!=''){
			$selectedArray = explode(',', $selected);
		}else if($detault<1){
			$selectedArray=array(30,10,0,-1,-10);
		}
		for ($i=30; $i >= -30 ; $i--) {
			if($i%5!=0 && $i!=-1){
				continue;
			} 
			$showText="Before $i Days";
			if($i<0){
				$showText="After ".($i*-1)." Days";
			}else if($i==0){
				$showText="Contract Last Days";
			}
			$selected = (in_array($i, $selectedArray))?'selected':'';			
		?>
		<option <?php echo $selected; ?> value="<?php echo $i; ?>"><?php echo $showText; ?></option>
		<?php
		}
		
	}
	function getMenuSubmenuDropdown($selected=''){
		$menuParent = $this->CI->Acc_Model->parentMenuList($this->CI->fx->masterClient);
		foreach($menuParent as $mParent){ ?>
			<optgroup label="<?php echo $mParent -> menu_name; ?>">
				<?php if($mParent->menu_type==1){ ?>
					<option value="<?php echo $mParent->menu_id; ?>"><?php echo $mParent -> menu_name; ?></option>
				<?php }
				$menuChild = $this-> CI -> Acc_Model->childMenuList($this->CI->fx->masterClient,$mParent->menu_id);
						if(isset($menuChild) && count($menuChild)>0){
							foreach($menuChild as $mChild){
				?>
				<option <?php if($selected==$mChild->menu_id) echo "selected"; ?> value="<?php echo $mChild->menu_id; ?>"><?php echo $mChild -> menu_name; ?></option>
				<?php } } ?>
				
			</optgroup>
		<?php } 
	}
	public function getSaleCategoryDropdown($selected=NULL,$queryArray=array()){
		 	foreach($this->CI->Acc_Model->getSaleCategory($queryArray,array()) as $dropdown){ ?>
			<option value="<?php echo $dropdown['id']; ?>" <?php if($dropdown['id']==$selected) echo ' selected'; ?>><?php echo $dropdown['sale_category_title']; ?></option>
	        <?php }
	}
	
	public function getPaginationRecords($selected=null){
		$paginationRecord = $this -> CI -> Acc_Model -> getPaginationRecords(array('status'=>1));
		if(count($paginationRecord)>0){
			foreach ($paginationRecord as $key => $value) { ?>
				<option <?php if($value['is_default']==1) echo 'selected'; ?> value="<?php echo ($value['record_val']==0)?1000000:$value['record_val']; ?>"> <?php echo $value['record_title'];?></option>
		<?php	}
		}else{ ?>
			<option value="10">10</option>
			<option value="25">25</option>
			<option value="50">50</option>
			<option value="100">100</option>
			<option value="200">200</option>
	<?php }
	}
	
	public function getUserDropdown($selected,$agent=1){
		$paginationRecord = $this -> CI -> Acc_Model -> getUserdropdownList(($agent==1)?array('is_agent'=>1):array());
		if(count($paginationRecord)>0){
		foreach ($paginationRecord as $key => $value) { ?>
			<option <?php if($selected==$value->client_id){ echo 'selected'; } ?> value="<?php echo $value->client_id; ?>"> <?php echo $value->clientName; ?></option>
		<?php }	
		} 
	}
	
	public function getCompChargesDropdown($status=NULL,$selected=NULL,$where=NULL){
		$whereSt = "1=1";
		if($status !== NULL) $whereSt .= " and status=".$status; 
		if($where != NULL)  $whereSt .= " and ".$where;
		foreach($this->CI->Acc_Model->getCompChargesList($whereSt,$this->CI->fx->clientCompDb) as $dropdown){ ?>
		<option value="<?php echo $dropdown->charge_id; ?>" <?php if($dropdown->charge_id==$selected) echo ' selected'; ?>><?php echo $dropdown->charge_name; ?></option>
        <?php }
	}
	
	public function getSelectDropdown($fieldname, $where = '', $selected = '')
	{
		$where2  = " 1=1 AND ".$fieldname."!='' ";
		$where2 .= ($where != '') ? ("AND $where") : '';
		foreach ($this->CI->Acc_Model->getSelectDropdown($fieldname, $where2)  as $key => $value) { if(empty($value[$fieldname]))continue; ?>
			<option data-option_type='0' value="<?php echo $value[$fieldname]; ?>" <?php echo ($selected == $value[$fieldname])? "selected":'';?>> 
				<?php echo $value[$fieldname]; ?>
			</option>
			<?php
		}
	}
	public function getMultiSelectDropdown($fieldname, $where = '', $selected = '') {
		$where2  = " 1=1 AND ".$fieldname."!='' ";
		$where2 .= ($where != '') ? ("AND $where") : '';
		$result	 = $this->CI->Acc_Model->getSelectDropdown($fieldname, $where2);
		$usedArr = array();
		$selectedArr	= explode(',',$selected);
		foreach ($result  as $key => $value) { ?>
			<?php $tmparr	= explode(',', $value[$fieldname]);
			foreach($tmparr as $optval) { if(in_array($optval, $usedArr)) continue;?>
			<option data-option_type='0' value="<?php echo $optval; ?>" <?php echo in_array($optval, $selectedArr)? "selected":'';?>> 
				<?php echo $optval; ?>
			</option>
			<?php
			$usedArr[] = $optval;
			}
		}
	}

	public function getInvoiceSelectDropdown($fieldname, $where = '', $selected = '', $tbl = 'comp_invoice') {
		$where2  = " 1=1 AND ".$fieldname."!='' ";
		$where2 .= ($where != '') ? ("AND $where") : '';
		foreach ($this->CI->Acc_Model->getSelectDropdown($fieldname, $where2, $tbl)  as $key => $value) { if(empty($value[$fieldname]))continue; ?>
			<option data-option_type='0' value="<?php echo $value[$fieldname]; ?>" <?php echo ($selected == $value[$fieldname])? "selected":'';?>> 
				<?php echo $value[$fieldname]; ?>
			</option>
			<?php
		}
	}
	public function noRecShow($pageNoAr=array(25,50,100,200,500)){
		foreach($pageNoAr as $val){
			echo '<option value="'.$val.'">'.$val.'</option>';
		}
	}
}

?>

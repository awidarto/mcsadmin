<?php

class Members extends Application
{

	public function __construct()
	{
		parent::__construct();
		$this->ag_auth->restrict('admin'); // restrict this controller to admins only
		$this->table_tpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">'
		);
		$this->table->set_template($this->table_tpl);

		$this->breadcrumb->add_crumb('Home','admin/dashboard');
		$this->breadcrumb->add_crumb('Users','admin/users/manage');

	}

	public function manage()
	{

		$this->breadcrumb->add_crumb('Members','admin/members/manage');

		$this->load->library('table');

		$this->table->set_heading(
			'Username', 
			'Email',
			'Full Name',
			//'Merchant Name',
			//'Bank Account',
			'Street',
			'District',
			'City',
			'Province',
			'Country',
			'ZIP',
			'Mobile',
			'Phone',
			'Group',
			'Created',
			'Actions'); // Setting headings for the table

		$this->table->set_footing(
			'<input type="text" name="search_username" id="search_username" value="Search delivery time" class="search_init" />',
			'<input type="text" name="search_email" id="search_email" value="Search zone" class="search_init" />',
			'<input type="text" name="search_fullname" value="Search full name" class="search_init" />',
			'<input type="text" name="search_street" value="Search street" class="search_init" />',
			'<input type="text" name="search_district" value="Search district" class="search_init" />',
			'<input type="text" name="search_city" value="Search city" class="search_init" />',
			'<input type="text" name="search_province" value="Search province" class="search_init" />',
			'<input type="text" name="search_country" value="Search country" class="search_init" />',
			'<input type="text" name="search_zip" value="Search ZIP" class="search_init" />',
			'<input type="text" name="search_mobile" value="Search mobile" class="search_init" />',
			'<input type="text" name="search_phone" value="Search phone" class="search_init" />',
			'<input type="text" name="search_created" id="search_timestamp" value="Search created" class="search_init" />',
			form_button('do_setgroup','Set Group','id="doSetGroup"')
			);

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/members/ajaxmanage';
		$page['add_button'] = array('link'=>'admin/members/add','label'=>'Add New Member');
		$page['page_title'] = 'Manage Members';
		$this->ag_auth->view('memberajaxlistview',$page); // Load the view
	}

	public function ajaxmanage(){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'username',
			'email',
			//'password',
			//'merchantname',
			'fullname',
			'street',
			'district',
			'city',
			'province',
			'country',
			'zip',
			'mobile',
			'phone',
			'created',
			'groupname',
			'bank',
			'account_number',
			'account_name',
			'group_id',
			'token',
			'identifier',
			'merchant_request',
			'success',
			'fail'
		);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_members_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_members_table'));

		$this->db->select('*,g.description as groupname');
		$this->db->join('groups as g','members.group_id = g.id','left');

		$search = false;
				//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
			$search = true;
		}

		if($this->input->post('sSearch_0') != ''){
			$this->db->like('username',$this->input->post('sSearch_0'));
			$search = true;
		}


		if($this->input->post('sSearch_1') != ''){
			$this->db->like('email',$this->input->post('sSearch_1'));
			$search = true;
		}

		if($this->input->post('sSearch_2') != ''){
			$this->db->like('fullname',$this->input->post('sSearch_2'));
			$search = true;
		}

		if($this->input->post('sSearch_3') != ''){
			$this->db->like('street',$this->input->post('sSearch_3'));
			$search = true;
		}

		if($this->input->post('sSearch_4') != ''){
			$this->db->like('district',$this->input->post('sSearch_4'));
			$search = true;
		}

		if($this->input->post('sSearch_5') != ''){
			$this->db->like('city',$this->input->post('sSearch_5'));
			$search = true;
		}
		if($this->input->post('sSearch_6') != ''){
			$this->db->like('province',$this->input->post('sSearch_6'));
			$search = true;
		}

		if($this->input->post('sSearch_7') != ''){
			$this->db->like('country',$this->input->post('sSearch_7'));
			$search = true;
		}

		if($this->input->post('sSearch_8') != ''){
			$this->db->like('zip',$this->input->post('sSearch_8'));
			$search = true;
		}

		if($this->input->post('sSearch_9') != ''){
			$this->db->like('mobile',$this->input->post('sSearch_9'));
			$search = true;
		}

		if($this->input->post('sSearch_10') != ''){
			$this->db->like('phone',$this->input->post('sSearch_10'));
			$search = true;
		}

		if($this->input->post('sSearch_11') != ''){
			$this->db->like('created',$this->input->post('sSearch_11'));
			$search = true;
		}

		if($search){
			//$this->db->and_();
		}		


		$data = $this->db
			->limit($limit_count, $limit_offset)
			->order_by($columns[$sort_col],$sort_dir)
			->get($this->config->item('jayon_members_table'));

		//print $this->db->last_query();

		$result = $data->result_array();

		$aadata = array();


		foreach($result as $value => $key)
		{
			$delete = anchor("admin/members/delete/".$key['id']."/", "Delete"); // Build actions links
			$editpass = anchor("admin/members/editpass/".$key['id']."/", "Password"); // Build actions links
			if($key['group_id'] === group_id('merchant')){
				$addapp = anchor("admin/members/merchantmanage/".$key['id']."/", "Applications"); // Build actions links
			}else{
				$addapp = '&nbsp'; // Build actions links
			}
			$edit = anchor("admin/members/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = form_checkbox('assign[]',$key['id'],FALSE,'class="assign_check"').' '.anchor("admin/members/details/".$key['id']."/", $key['username']); // Build detail links
			
			$aadata[] = array(
				$detail,
			 	$key['email'],
			 	$key['fullname'],
				$key['street'],
				$key['district'],
				$key['city'],
				$key['province'],
				$key['country'],
				$key['zip'],
			 	//$key['merchantname'],
			 	//$key['bank'].'<br/>'.$key['account_number'].'<br/>'.$key['account_name'],
			 	$key['mobile'],
			 	$key['phone'],
			 	$key['created'],
			 	$key['groupname'],
			 	$edit.' '.$editpass.' '.$delete
			); // Adding row to table

		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);
	}

	public function merchant()
	{

		$this->breadcrumb->add_crumb('Manage Merchants','admin/members/merchant');

		$this->load->library('table');

		$this->table->set_heading(
			'Username', 
			'Email',
			'Full Name',
			//'Merchant Name',
			//'Bank Account',
			'Street',
			'District',
			'City',
			'Province',
			'Country',
			'ZIP',
			'Mobile',
			'Phone',
			//'Group',
			'Created',
			'Actions'); // Setting headings for the table

		$this->table->set_footing(
			'<input type="text" name="search_username" id="search_username" value="Search delivery time" class="search_init" />',
			'<input type="text" name="search_email" id="search_email" value="Search zone" class="search_init" />',
			'<input type="text" name="search_fullname" value="Search full name" class="search_init" />',
			'<input type="text" name="search_street" value="Search street" class="search_init" />',
			'<input type="text" name="search_district" value="Search district" class="search_init" />',
			'<input type="text" name="search_city" value="Search city" class="search_init" />',
			'<input type="text" name="search_province" value="Search province" class="search_init" />',
			'<input type="text" name="search_country" value="Search country" class="search_init" />',
			'<input type="text" name="search_zip" value="Search ZIP" class="search_init" />',
			'<input type="text" name="search_mobile" value="Search mobile" class="search_init" />',
			'<input type="text" name="search_phone" value="Search phone" class="search_init" />',
			'<input type="text" name="search_created" id="search_timestamp" value="Search created" class="search_init" />',
			form_button('do_setgroup','Set Group','id="doSetGroup"')
			);

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/members/ajaxmerchant';
		$page['add_button'] = array('link'=>'admin/members/merchant/add','label'=>'Add New Member');
		$page['page_title'] = 'Manage Merchants';
		$this->ag_auth->view('memberajaxlistview',$page); // Load the view
	}

	public function ajaxmerchant(){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$group_id = user_group_id('merchant');

		$columns = array(
			'username',
			'email',
			//'password',
			//'merchantname',
			'fullname',
			'street',
			'district',
			'city',
			'province',
			'country',
			'zip',
			'mobile',
			'phone',
			'groupname',
			'created',
			'bank',
			'account_number',
			'account_name',
			'group_id',
			'token',
			'identifier',
			'merchant_request',
			'success',
			'fail'
		);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_members_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_members_table'));

		//$this->db->select('*,g.description as groupname');
		//$this->db->join('groups as g','members.group_id = g.id','left');

		$search = false;
				//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
			$search = true;
		}

		if($this->input->post('sSearch_0') != ''){
			$this->db->like('username',$this->input->post('sSearch_0'));
			$search = true;
		}


		if($this->input->post('sSearch_1') != ''){
			$this->db->like('email',$this->input->post('sSearch_1'));
			$search = true;
		}

		if($this->input->post('sSearch_2') != ''){
			$this->db->like('fullname',$this->input->post('sSearch_2'));
			$search = true;
		}

		if($this->input->post('sSearch_3') != ''){
			$this->db->like('street',$this->input->post('sSearch_3'));
			$search = true;
		}

		if($this->input->post('sSearch_4') != ''){
			$this->db->like('district',$this->input->post('sSearch_4'));
			$search = true;
		}

		if($this->input->post('sSearch_5') != ''){
			$this->db->like('city',$this->input->post('sSearch_5'));
			$search = true;
		}
		if($this->input->post('sSearch_6') != ''){
			$this->db->like('province',$this->input->post('sSearch_6'));
			$search = true;
		}

		if($this->input->post('sSearch_7') != ''){
			$this->db->like('country',$this->input->post('sSearch_7'));
			$search = true;
		}

		if($this->input->post('sSearch_8') != ''){
			$this->db->like('zip',$this->input->post('sSearch_8'));
			$search = true;
		}

		if($this->input->post('sSearch_9') != ''){
			$this->db->like('mobile',$this->input->post('sSearch_9'));
			$search = true;
		}

		if($this->input->post('sSearch_10') != ''){
			$this->db->like('phone',$this->input->post('sSearch_10'));
			$search = true;
		}

		if($this->input->post('sSearch_11') != ''){
			$this->db->like('created',$this->input->post('sSearch_11'));
			$search = true;
		}

		if($search){
			//$this->db->and_();
		}		


		$data = $this->db
			->where('group_id',$group_id)
			->limit($limit_count, $limit_offset)
			->order_by($columns[$sort_col],$sort_dir)
			->get($this->config->item('jayon_members_table'));

		//print $this->db->last_query();

		$result = $data->result_array();

		$aadata = array();


		foreach($result as $value => $key)
		{
			$delete = anchor("admin/members/delete/".$key['id']."/", "Delete"); // Build actions links
			$editpass = anchor("admin/members/editpass/".$key['id']."/", "Password"); // Build actions links
			if($key['group_id'] === group_id('merchant')){
				$addapp = anchor("admin/members/merchant/apps/manage/".$key['id']."/", "Applications"); // Build actions links
			}else{
				$addapp = '&nbsp'; // Build actions links
			}
			$edit = anchor("admin/members/merchant/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = form_checkbox('assign[]',$key['id'],FALSE,'class="assign_check"').' '.anchor("admin/members/details/".$key['id']."/", '<span id="un_'.$key['id'].'">'.$key['username'].'</span>'); // Build detail links
			
			$aadata[] = array(
				$detail,
			 	$key['email'],
			 	$key['fullname'],
				$key['street'],
				$key['district'],
				$key['city'],
				$key['province'],
				$key['country'],
				$key['zip'],
			 	//$key['merchantname'],
			 	//$key['bank'].'<br/>'.$key['account_number'].'<br/>'.$key['account_name'],
			 	$key['mobile'],
			 	$key['phone'],
			 	$key['created'],
			 	$addapp.' '.$edit.' '.$editpass.' '.$delete
			); // Adding row to table

		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);
	}

	public function buyer()
	{

		$this->breadcrumb->add_crumb('Manage Buyers','admin/members/buyer');

		$this->load->library('table');

		$this->table->set_heading(
			'Username', 
			'Email',
			'Full Name',
			//'Merchant Name',
			//'Bank Account',
			'Street',
			'District',
			'City',
			'Province',
			'Country',
			'ZIP',
			'Mobile',
			'Phone',
			//'Group',
			'Created',
			'Actions'); // Setting headings for the table

		$this->table->set_footing(
			'<input type="text" name="search_username" id="search_username" value="Search delivery time" class="search_init" />',
			'<input type="text" name="search_email" id="search_email" value="Search zone" class="search_init" />',
			'<input type="text" name="search_fullname" value="Search full name" class="search_init" />',
			'<input type="text" name="search_street" value="Search street" class="search_init" />',
			'<input type="text" name="search_district" value="Search district" class="search_init" />',
			'<input type="text" name="search_city" value="Search city" class="search_init" />',
			'<input type="text" name="search_province" value="Search province" class="search_init" />',
			'<input type="text" name="search_country" value="Search country" class="search_init" />',
			'<input type="text" name="search_zip" value="Search ZIP" class="search_init" />',
			'<input type="text" name="search_mobile" value="Search mobile" class="search_init" />',
			'<input type="text" name="search_phone" value="Search phone" class="search_init" />',
			'<input type="text" name="search_created" id="search_timestamp" value="Search created" class="search_init" />',
			form_button('do_setgroup','Set Group','id="doSetGroup"')
			);

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/members/ajaxbuyer';
		$page['add_button'] = array('link'=>'admin/members/buyer/add','label'=>'Add New Member');
		$page['page_title'] = 'Manage Buyers';
		$this->ag_auth->view('memberajaxlistview',$page); // Load the view
	}

	public function ajaxbuyer(){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$group_id = user_group_id('buyer');

		$columns = array(
			'username',
			'email',
			//'password',
			//'merchantname',
			'fullname',
			'street',
			'district',
			'city',
			'province',
			'country',
			'zip',
			'mobile',
			'phone',
			'groupname',
			'created',
			'bank',
			'account_number',
			'account_name',
			'group_id',
			'token',
			'identifier',
			'merchant_request',
			'success',
			'fail'
		);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_members_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_members_table'));

		//$this->db->select('*,g.description as groupname');
		//$this->db->join('groups as g','members.group_id = g.id','left');

		$search = false;
				//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
			$search = true;
		}

		if($this->input->post('sSearch_0') != ''){
			$this->db->like('username',$this->input->post('sSearch_0'));
			$search = true;
		}


		if($this->input->post('sSearch_1') != ''){
			$this->db->like('email',$this->input->post('sSearch_1'));
			$search = true;
		}

		if($this->input->post('sSearch_2') != ''){
			$this->db->like('fullname',$this->input->post('sSearch_2'));
			$search = true;
		}

		if($this->input->post('sSearch_3') != ''){
			$this->db->like('street',$this->input->post('sSearch_3'));
			$search = true;
		}

		if($this->input->post('sSearch_4') != ''){
			$this->db->like('district',$this->input->post('sSearch_4'));
			$search = true;
		}

		if($this->input->post('sSearch_5') != ''){
			$this->db->like('city',$this->input->post('sSearch_5'));
			$search = true;
		}
		if($this->input->post('sSearch_6') != ''){
			$this->db->like('province',$this->input->post('sSearch_6'));
			$search = true;
		}

		if($this->input->post('sSearch_7') != ''){
			$this->db->like('country',$this->input->post('sSearch_7'));
			$search = true;
		}

		if($this->input->post('sSearch_8') != ''){
			$this->db->like('zip',$this->input->post('sSearch_8'));
			$search = true;
		}

		if($this->input->post('sSearch_9') != ''){
			$this->db->like('mobile',$this->input->post('sSearch_9'));
			$search = true;
		}

		if($this->input->post('sSearch_10') != ''){
			$this->db->like('phone',$this->input->post('sSearch_10'));
			$search = true;
		}

		if($this->input->post('sSearch_11') != ''){
			$this->db->like('created',$this->input->post('sSearch_11'));
			$search = true;
		}

		if($search){
			//$this->db->and_();
		}		

		$data = $this->db
			->where('group_id',$group_id)
			->limit($limit_count, $limit_offset)
			->order_by($columns[$sort_col],$sort_dir)
			->get($this->config->item('jayon_members_table'));

		//print $this->db->last_query();

		$result = $data->result_array();

		$aadata = array();


		foreach($result as $value => $key)
		{
			$delete = anchor("admin/members/delete/".$key['id']."/", "Delete"); // Build actions links
			$editpass = anchor("admin/members/editpass/".$key['id']."/", "Password"); // Build actions links
			if($key['group_id'] === group_id('merchant')){
				$addapp = anchor("admin/members/merchantmanage/".$key['id']."/", "Applications"); // Build actions links
			}else{
				$addapp = '&nbsp'; // Build actions links
			}
			$edit = anchor("admin/members/buyer/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = form_checkbox('assign[]',$key['id'],FALSE,'class="assign_check"').' '.anchor("admin/members/details/".$key['id']."/", '<span id="un_'.$key['id'].'">'.$key['username'].'</span>'); // Build detail links
			
			$aadata[] = array(
				$detail,
			 	$key['email'],
			 	$key['fullname'],
				$key['street'],
				$key['district'],
				$key['city'],
				$key['province'],
				$key['country'],
				$key['zip'],
			 	//$key['merchantname'],
			 	//$key['bank'].'<br/>'.$key['account_number'].'<br/>'.$key['account_name'],
			 	$key['mobile'],
			 	$key['phone'],
			 	$key['created'],
			 	$edit.' '.$editpass.' '.$delete
			); // Adding row to table

		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);
	}

	function details($id){
		$this->load->library('table');

		$user = $this->get_user($id);

		foreach($user as $key=>$val){
			$this->table->add_row($key,$val); // Adding row to table
		}

		$page['page_title'] = 'Member Info';
		$this->ag_auth->view('members/details',$page);
	}

	public function ajaxsetgroup(){
		$users = $this->input->post('users');
		$setgroup = $this->input->post('set_group');

		if(is_array($users)){
			foreach ($users as $u) {
				$this->db->where('id',$u)->update($this->config->item('jayon_members_table'),array('group_id'=>$setgroup));
				/*
				$data = array(
						'timestamp'=>date('Y-m-d h:i:s',time()),
						'report_timestamp'=>date('Y-m-d h:i:s',time()),
						'delivery_id'=>$d,
						'device_id'=>'',
						'courier_id'=>'',
						'actor_type'=>'AD',
						'actor_id'=>$this->session->userdata('userid'),
						'latitude'=>'',
						'longitude'=>'',
						'status'=>$this->config->item('trans_status_canceled'),
						'req_by' => $req_by,
						'req_name' => $req_name,
						'req_note' => $req_note,
						'notes'=>''
						);

				delivery_log($data);
				*/
			}
		}else{

			$this->db->where('id',$users)->update($this->config->item('jayon_members_table'),array('group_id'=>$setgroup));

			/*
			$data = array(
					'timestamp'=>date('Y-m-d h:i:s',time()),
					'report_timestamp'=>date('Y-m-d h:i:s',time()),
					'delivery_id'=>$delivery_id,
					'device_id'=>'',
					'courier_id'=>'',
					'actor_type'=>'AD',
					'actor_id'=>$this->session->userdata('userid'),
					'latitude'=>'',
					'longitude'=>'',
					'status'=>'change_group',
					'req_by' => $req_by,
					'req_name' => $req_name,
					'req_note' => $req_note,
					'notes'=>''
					);

			delivery_log($data);
			*/

		}

		print json_encode(array('result'=>'ok'));

		//send_notification('Cancelled Orders',$buyeremail,null,'rescheduled_order_buyer',$edata,null);

	}

	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->config->item('jayon_members_table'));
		$page['page_title'] = 'Delete Member';
		$this->ag_auth->view('members/delete_success',$page);
	}

	public function get_user($id){
		$result = $this->db->where('id', $id)->get($this->config->item('jayon_members_table'));
		if($result->num_rows() > 0){
			return $result->row_array();
		}else{
			return false;
		}
	}

	public function get_group(){
		$this->db->select('id,description');
		$result = $this->db->get($this->ag_auth->config['auth_group_table']);
		foreach($result->result_array() as $row){
			$res[$row['id']] = $row['description'];
		}
		return $res;
	}

	public function get_group_description($id){
		$this->db->select('description');
		if(!is_null($id)){
			$this->db->where('id',$id);
		}
		$result = $this->db->get($this->ag_auth->config['auth_group_table']);
		$row = $result->row();
		return $row->description;
	}

	public function update_user($id,$data){
		$result = $this->db->where('id', $id)->update($this->config->item('jayon_members_table'),$data);
		return $this->db->affected_rows();
	}


	public function add()
	{
		if(in_array('merchant',$this->uri->segment_array())){
			$this->breadcrumb->add_crumb('Manage Merchants','admin/members/merchant');
			$this->breadcrumb->add_crumb('Add Merchant','admin/members/merchant/add');
			$data['page_title'] = 'Add Merchant';

			$back_url = 'admin/members/merchant';
			$success_url = 'admin/members/merchant';
			$error_url = 'admin/members/merchant/add';

			$utype = 'Merchant';
		}else if(in_array('buyer',$this->uri->segment_array())){
			$this->breadcrumb->add_crumb('Manage Buyers','admin/members/buyer');
			$this->breadcrumb->add_crumb('Add Buyer','admin/members/buyer/add');
			$data['page_title'] = 'Add Buyer';

			$back_url = 'admin/members/buyer';
			$success_url = 'admin/members/buyer';
			$error_url = 'admin/members/buyer/add';

			$utype = 'Buyer';			
		}

		$this->form_validation->set_rules('username', 'Username', 'required|min_length[6]|callback_field_exists');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'required|min_length[6]|matches[password]');
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email|callback_field_exists');
		$this->form_validation->set_rules('fullname', 'Full Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('merchantname', 'Merchant Name', 'trim|xss_clean');
		$this->form_validation->set_rules('bank', 'Bank', 'trim|xss_clean');
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|xss_clean');
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|xss_clean');
		$this->form_validation->set_rules('street', 'Street', 'required|trim|xss_clean');
		$this->form_validation->set_rules('district', 'District', 'required|trim|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'required|trim|xss_clean');
		$this->form_validation->set_rules('province', 'Province', 'required|trim|xss_clean');
		$this->form_validation->set_rules('country', 'Country', 'required|trim|xss_clean');
		$this->form_validation->set_rules('zip', 'ZIP', 'required|trim|xss_clean');
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');


		$this->form_validation->set_rules('same_as_personal_address', 'Same As Personal Address', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_street', 'Street', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_district', 'District', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_city', 'City', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_country', 'Country', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_province', 'Province', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_zip', 'ZIP', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_phone', 'Phone Number', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_mobile', 'Mobile Number', 'trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = array(
				group_id('merchant')=>group_desc('merchant'),
				group_id('buyer')=>group_desc('buyer')
			);
			$data['back_url'] = anchor($back_url,'Cancel');
			$this->ag_auth->view('members/add',$data);
		}
		else
		{
			$username = set_value('username');
			$password = $this->ag_auth->salt(set_value('password'));
			$fullname = set_value('fullname');
			$merchantname = set_value('merchantname');
			$bank = set_value('bank');
			$account_number = set_value('account_number');
			$account_name = set_value('account_name');
			$street = set_value('street');
			$district = set_value('district');
			$province = set_value('province');
			$city = set_value('city');
			$country = set_value('country');
			$zip = set_value('zip');
			$phone= set_value('phone');
			$mobile= set_value('mobile');
			$email = set_value('email');

			$same_as_personal_address = set_value('same_as_personal_address');

			$mc_street = set_value('mc_street');
			$mc_district = set_value('mc_district');
			$mc_province = set_value('mc_province');
			$mc_city = set_value('mc_city');
			$mc_country = set_value('mc_country');
			$mc_zip = set_value('mc_zip');
			$mc_phone= set_value('mc_phone');
			$mc_mobile= set_value('mc_mobile');

			$group_id = set_value('group_id');

			$dataset = array(
				'username'=>$username,
				'password'=>$password,
				'fullname'=>$fullname,
				'merchantname'=>$merchantname,
				'bank'=>$bank,
				'account_number'=>$account_number,
				'account_name'=>$account_name,
				'street'=>$street,
				'district'=>$district,
				'province'=>$province,
				'city'=>$city,
				'country'=>$country,
				'zip'=>$zip,
				'phone'=>$phone,
				'mobile'=>$mobile,
				'email'=>$email,

				'same_as_personal_address' =>$same_as_personal_address,
				'mc_street' =>$mc_street,
				'mc_district' =>$mc_district,
				'mc_province' =>$mc_province,
				'mc_city' =>$mc_city,
				'mc_country' =>$mc_country,
				'mc_zip' =>$mc_zip,
				'mc_phone'=>$mc_phone,
				'mc_mobile'=>$mc_mobile,

				'group_id'=>$group_id,
				'created'=> date('Y-m-d h:i:s',time())

			);

			if($this->db->insert($this->config->item('jayon_members_table'),$dataset) === TRUE)
			{
				$data['message'] = "The user account has now been created.";
				$data['page_title'] = 'Add Member';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
				$this->ag_auth->view('message', $data);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account has not been created.";
				$data['page_title'] = 'Add Member Error';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()

	public function edit($id)
	{
		if(in_array('merchant',$this->uri->segment_array())){
			$this->breadcrumb->add_crumb('Manage Merchants','admin/members/merchant');
			$this->breadcrumb->add_crumb('Edit Merchant','admin/members/merchant/edit/'.$id);
			$data['page_title'] = 'Edit Merchant';

			$back_url = 'admin/members/merchant';
			$success_url = 'admin/members/merchant';
			$error_url = 'admin/members/merchant/edit/'.$id;

			$utype = 'Merchant';

		}else if(in_array('buyer',$this->uri->segment_array())){
			$this->breadcrumb->add_crumb('Manage Buyers','admin/members/buyer');
			$this->breadcrumb->add_crumb('Edit Buyer','admin/members/buyer/edit/'.$id);
			$data['page_title'] = 'Edit Buyer';

			$back_url = 'admin/members/buyer';
			$success_url = 'admin/members/buyer';
			$error_url = 'admin/members/buyer/edit/'.$id;

			$utype = 'Buyer';
		}

		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email');
		$this->form_validation->set_rules('fullname', 'Full Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('merchantname', 'Merchant Name', 'trim|xss_clean');
		$this->form_validation->set_rules('bank', 'Bank', 'trim|xss_clean');
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|xss_clean');
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|xss_clean');
		$this->form_validation->set_rules('street', 'Street', 'required|trim|xss_clean');
		$this->form_validation->set_rules('district', 'District', 'required|trim|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'required|trim|xss_clean');
		$this->form_validation->set_rules('province', 'Province', 'required|trim|xss_clean');
		$this->form_validation->set_rules('country', 'Country', 'required|trim|xss_clean');
		$this->form_validation->set_rules('zip', 'ZIP', 'required|trim|xss_clean');
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');

		$this->form_validation->set_rules('same_as_personal_address', 'Same As Personal Address', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_street', 'Street', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_district', 'District', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_city', 'City', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_province', 'Province', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_country', 'Country', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_zip', 'ZIP', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_phone', 'Phone Number', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_mobile', 'Mobile Number', 'trim|xss_clean');

		$user = $this->get_user($id);
		$data['user'] = $user;

		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = array(
				group_id('merchant')=>group_desc('merchant'),
				group_id('buyer')=>group_desc('buyer')
			);
			$data['back_url'] = anchor($back_url,'Cancel');
			$this->ag_auth->view('members/edit',$data);
		}
		else
		{

			$dataset['fullname'] = set_value('fullname');
			$dataset['merchantname'] = set_value('merchantname');
			$dataset['bank'] = set_value('bank');
			$dataset['account_name'] = set_value('account_name');
			$dataset['account_number'] = set_value('account_number');
			$dataset['street'] = set_value('street');
			$dataset['district'] = set_value('district');
			$dataset['province'] = set_value('province');
			$dataset['city'] = set_value('city');
			$dataset['country'] = set_value('country');
			$dataset['zip'] = set_value('zip');
			$dataset['phone'] = set_value('phone');
			$dataset['mobile'] = set_value('mobile');
			$dataset['email'] = set_value('email');
			$dataset['group_id'] = set_value('group_id');

			$dataset['same_as_personal_address'] = set_value('same_as_personal_address');
			$dataset['mc_street'] = set_value('mc_street');
			$dataset['mc_district'] = set_value('mc_district');
			$dataset['mc_province'] = set_value('mc_province');
			$dataset['mc_city'] = set_value('mc_city');
			$dataset['mc_country'] = set_value('mc_country');
			$dataset['mc_zip'] = set_value('mc_zip');
			$dataset['mc_phone'] = set_value('mc_phone');
			$dataset['mc_mobile'] = set_value('mc_mobile');


			if($this->db->where('id',$id)->update($this->config->item('jayon_members_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$this->oi->add_success($utype.' updated & saved');
				redirect($success_url);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$this->oi->add_success('Failed to update '.$utype);
				redirect($error_url);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()

	public function editpass($id)
	{
		$this->form_validation->set_rules('password', 'Password', 'min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'min_length[6]|matches[password]');

		$user = $this->get_user($id);
		$data['user'] = $user;

		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = $this->get_group();
			$data['page_title'] = 'Change Member Password';
			$this->ag_auth->view('members/editpass',$data);
		}
		else
		{
			$result = TRUE;
			$dataset['password'] = $this->ag_auth->salt(set_value('password'));

			//if( $result = $this->update_user($id,$dataset))
			if($this->db->where('id',$id)->update($this->config->item('jayon_members_table'),$dataset) === TRUE)
			{
				$data['message'] = "The user password has now updated.";
				$data['page_title'] = 'Edit Member Password Success';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
				$this->ag_auth->view('message', $data);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account failed to update.";
				$data['page_title'] = 'Edit Member Password Error';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()
	// WOKRING ON PROPER IMPLEMENTATION OF ADDING & EDITING USER ACCOUNTS
}

?>
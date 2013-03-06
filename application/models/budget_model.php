<?php

class Budget_model extends CI_Model {
	//put your code here
	//
	
	function get_all()
	{
		$query = $this->db->get("accounts");
		return $query->result();
	}
	function get_accounts_for_owner($owner)
	{
		$this->db->where('owner', $owner);
		$query = $this->db->get("accounts");
		return $query->result_array();
	}
	function get_categories_for_owner($owner)
	{
		$this->db->where('owner', $owner);
		$query = $this->db->get("categories");
		return $query->result_array();
	}
	function get_transactions_for_owner($owner)
	{
		$this->db->select('transactions.*,accounts.name as account_name, category_name');
		$this->db->from('transactions');
		$this->db->join('accounts', 'transactions.account_id=accounts.id');
		$this->db->join('categories', 'transactions.category_id=categories.id', 'left');		
		$this->db->where('accounts.owner', $owner);
		$query = $this->db->get();
		return $query->result_array();
		
		//$query = $this->db->query('select * from transactions where account_id in (select id from accounts where owner=?)', array($owner));	
		//$query = $this->db->get('transactions');
		
		//return $query->result_array();
	}
	function get_category_by_id($id)
	{
		return $this->get_object_by_id($id, 'categories');		
	}
	function get_object_by_id($id, $table)
	{
		$this->db->where('id', $id);
		$query = $this->db->get($table);
		
		if ($query->num_rows() > 0)
		{
			$row = array_pop($query->result_array());
			return $row;
		} else
		{
			return NULL;
		}	
	}
	function get_account_by_id($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('accounts');
		
		if ($query->num_rows() > 0)
		{
			$row = array_pop($query->result_array());
			return $row;
		} else
		{
			return NULL;
		}
	}
	function get_transaction_by_id($id)
	{
		$this->db->select('transactions.*,accounts.name as account_name, category_name');
		$this->db->from('transactions');
		$this->db->join('accounts', 'transactions.account_id=accounts.id');
		$this->db->join('categories', 'transactions.category_id=categories.id', 'left');		
		$this->db->where('transactions.id', $id);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			$row = array_pop($query->result_array());
			return $row;
		} else
		{
			return NULL;
		}
	}
	function get_transaction_owner($id)
	{
		$query = $this->db->query('select owner from accounts where id= (select account_id from transactions where id =?)', array($id));	
		//$query = $this->db->get('transactions');
		
		if ($query->num_rows() > 0)
		{
			$row = array_pop($query->result_array());
			return $row['owner'];
		} else
		{
			return NULL;
		}
	}
	function get_transactions($id, $per_page = 10, $page = 0)
	{

		
		
		$this->db->select('transactions.*,accounts.name as account_name, category_name');
		$this->db->from('transactions');
		$this->db->join('accounts', 'transactions.account_id=accounts.id');
		$this->db->join('categories', 'transactions.category_id=categories.id', 'left');	
		$this->db->where('transactions.account_id', $id);
		$this->db->order_by('transaction_date', 'asc');
		$this->db->order_by('transactions.id', 'asc');
		$this->db->limit($per_page, $page);
		$query = $this->db->get();
		return $query->result_array();
		
	}
	function get_total_rows_for_account($id)
	{
		$this->db->where('account_id', $id);
		return $this->db->get('transactions')->num_rows();
		
	}
	function get_total_rows_for_category($id)
	{
		$this->db->where('category_id', $id);
		return $this->db->get('transactions')->num_rows();
		
	}
	function get_transactions_for_category($id)
	{
		$this->db->select('transactions.*,accounts.name as account_name, category_name');
		$this->db->from('transactions');
		$this->db->join('accounts', 'transactions.account_id=accounts.id');
		$this->db->join('categories', 'transactions.category_id=categories.id', 'left');		
		$this->db->where('transactions.category_id', $id);
		$query = $this->db->get();
		return $query->result_array();
		
	}
	function can_view($id, $username, $object)
	{
		$obj = $this->get_object_by_id($id, $object);
		if ($obj['owner'] == $username)
		{
			return true;
		} else
		{
			return false;
		}	
	}
	function can_view_transaction($id, $username)
	{
		$transaction = $this->get_transaction_by_id($id);
		if ($transaction)
		{
			return($this->can_view_account($transaction['account_id'], $username));
		} else
		{
			return $transaction;
		}
		
		
	}
	function can_view_account($id, $username)
	{
		$account = $this->get_account_by_id($id);
		if ($account['owner'] == $username)
		{
			return true;
		} else
		{
			return false;
		}
	}
	function create_category($data)
	{
		$insert = $this->db->insert('categories', $data);
		if ($insert)
		{
			$id = $this->db->insert_id();
			$insert = $this->get_category_by_id($id);
			$data['action'] = 'insert';
			$data['id'] = $id;
			$this->db->insert('categories_history', $data);
		}
		return $insert;		
	}
	
	function create($data)
	{
		
		$insert = $this->db->insert('accounts', $data);
		if ($insert)
		{
			$id = $this->db->insert_id();
			$insert = $this->get_account_by_id($id);
			$data['action'] = 'insert';
			$data['id'] = $id;
			$this->db->insert('accounts_history', $data);
			$this->update_current_balance($id);
		}
		return $insert;
	}
	function create_imported_transaction($data)
	{
		$vars = array();
		$vars['account_id'] = $data['account_id'];
		$vars['check_number'] = $data['check_number'];
		$vars['payee'] = $data['payee'];
		$vars['memo'] = $data['memo'];
		$vars['transaction_date'] = $data['transaction_date'];
		$vars['category_name'] = $data['category_name'];
		$vars['created_by'] = $data['username'];
		$vars['updated_by'] = $data['username'];
		
		$insert = $this->db->insert('imported_transactions', $vars);
		if ($insert)
		{
			$id = $this->db->insert_id();
			// process splits if they exist.
		}
		return $insert;
		
	}
	function get_imported_transactions_for_account($id)
	{
		$this->db->from('imported_transactions');
		$this->db->where('account_id', $id);
		$query = $this->db->get();

		return $query->result_array();
	}
	function create_transaction($data)
	{
		$insert = $this->db->insert('transactions', $data);
		if ($insert)
		{
			$id = $this->db->insert_id();
			$insert = $this->get_transaction_by_id($id);
			$data['action'] = 'insert';
			$data['id'] = $id;
			$this->db->insert('transactions_history', $data);
		}
		return $insert;
		
	}
	function update_account($data)
	{
		return $this->update_object($data, "accounts");		
	}
	function update_object($data, $table)
	{
		$obj = $this->get_object_by_id($data['id'], $table);
		$update = false;
		if ($obj)
		{
			$this->db->where('id', $data['id']);
			$update = $this->db->update($table, $data);
			if ($update)
			{
				$obj = $this->get_object_by_id($data['id'], $table);
				$obj['action'] = 'update';
				
				$this->db->insert($table . '_history', $obj);
				
			}
		}
		return $update;	
	}
	function update_transaction($data)
	{
		$table = 'transactions';
		$update = $this->update_object($data, $table);	
		if ($update)
		{
			//no need to do this here because we do this in the transactions controller
			//so that we can update both accounts if we are moving the transaction
			//to a different account
			//$this->update_current_balance($data['account_id']);
			
		}
		return $update;
	}	
	function delete_object($id, $table)
	{
		$obj = $this->get_object_by_id($id, $table);
		$delete = false;
		
		if ($obj)
		{
			$this->db->where('id', $id);
			$delete = $this->db->delete($table);
			if ($delete)
			{
				$obj['action'] = 'delete';
				$this->db->insert($table . '_history', $obj);
			}
		}
		return $delete;		
	}
	function delete_transaction($id)
	{
		$table = "transactions";
		$delete = $this->delete_object($id, $table);
		$this->update_current_balance($id);
		return $delete;
		
	}
	function update_current_balance($id)
	{
		$query = $this->db->query('update accounts set current_balance = initial_balance + (select sum(amount) from transactions where account_id=?) where id=?', array($id,$id));
		$this->check_account_balances($id);
		
	}
	function delete_category($id)
	{
		$table = "categories";
		return $this->delete_object($id, $table);		
	}
	function delete_account($id)
	{
		$table = "accounts";
		return $this->delete_object($id, $table);		
	}
	
	function get_last_insert_id()
	{
		return $this->db->insert_id();
	}
	function check_account_balances($id)
	{
		$account = $this->get_account_by_id($id);
		
		$this->db->select('*');
		$this->db->from('transactions');	
		$this->db->where('account_id', $id);
		$this->db->order_by('transaction_date', 'asc');
		$this->db->order_by('id', 'asc');
		
		$query = $this->db->get();
		$rows = $query->result_array();
		$current_balance = $account['initial_balance'];
		
		foreach ($rows as $row)
		{
			$current_balance = $current_balance + $row['amount'];
			$update = $this->db->query('update transactions set balance=? where id=?', array($current_balance, $row['id']));
		}
		
	}
}

?>

<?php

return [

    'status' => [
        'assigned' => 'Assigned Clients',
    ],

    'titles' => [
    	'create' => 'Create New Product',
    	'update' => 'Update client',
    ],

    'headers' => [
        'product_id' => 'Product ID',
        'cname' => 'Chinese Name',
        'ename' => 'English Name',
        'brand' => 'Brand',
        'supplier' => 'Supplier',
        'updated_at' => 'Last Modified Time',
    ],

    'tabs' => [
    	'tasks' => 'Tasks',
    	'leads' => 'Leads',
    	'documents' => 'Documents',
    	'invoices' => 'Invoices',
    	'all_tasks' => 'All tasks',
    	'all_leads' => 'All leads',
    	'all_documents' => 'All documents',
    	'max_size' => 'Max 5MB pr. file',
    	//Headers on tables in tables
    		'headers' => [
    		//Title && Leads
    			'title' => 'Title',
    			'assigned' => 'Assigned user',
    			'created_at' => 'Created at',
    			'deadline' => 'Deadline',
    			'new_task' => 'Add new task',
    			'new_lead' => 'Add new lead',
    			//Documments
		    	'file' => 'File',
    			'size' => 'Size',
    			//Invoices
    			'id' => 'ID',
    			'hours' => 'Hours',
    			'total_amount' => 'Total amount',
    			'invoice_sent' => 'Invoice sent',
    			'payment_received' => 'Payment received',
    		],
    ],
];

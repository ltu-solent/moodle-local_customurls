<?php
$capabilities = array(
    'local/customurls:managecustomurls' => array(
        'riskbitmask'  => RISK_SPAM | RISK_PERSONAL | RISK_XSS | RISK_CONFIG,
        'captype'      => 'view',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array(
            'student'        => CAP_PROHIBIT,
            'teacher'        => CAP_PROHIBIT,
            'editingteacher' => CAP_PROHIBIT,
            'manager'          => CAP_ALLOW
			)
			
    ),
	'local/customurls:librarycustomurls' => array(
        'riskbitmask'  => RISK_SPAM | RISK_PERSONAL | RISK_XSS | RISK_CONFIG,
        'captype'      => 'view',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array(
            'student'        => CAP_PROHIBIT,
            'teacher'        => CAP_PROHIBIT,
            'editingteacher' => CAP_PROHIBIT,
            'manager'          => CAP_ALLOW
			)
			
    ),
	'local/customurls:admincustomurls' => array(
        'riskbitmask'  => RISK_SPAM | RISK_PERSONAL | RISK_XSS | RISK_CONFIG,
        'captype'      => 'view',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array(
            'student'        => CAP_PROHIBIT,
            'teacher'        => CAP_PROHIBIT,
            'editingteacher' => CAP_PROHIBIT,
            'manager'          => CAP_ALLOW
			)
			
    ),
	
);
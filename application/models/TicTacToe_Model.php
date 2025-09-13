<?php

class TicTacToe_Model extends CI_Model {
    
    var $nRoundId = 0;
    
    function __construct() {
        
        parent::__construct();
        
    }
    
    function saveRound() {
        
        $aParams = [
            
            'starter' => 'H',
            'difficulty' => $this->input->post('sDifficulty')
            
        ];

        $this->db->insert('rounds', $aParams);
        
        $nInsertId = $this->db->insert_id();
        $aReturn = [
            
            'success' => true,
            'id' => $nInsertId
            
        ];
        
        return $aReturn;
        
    }
    
    function saveTurn() {
        
        $aParams = [
            
            'round_id' => $this->input->post('nRoundId'),
            'char' => $this->input->post('sCharacter'),
            'position' => $this->input->post('nIndex')
            
        ];
        
        $this->db->insert('turns', $aParams);
        
        $aReturn = [
            
            'success' => true
            
        ];
        
        return $aReturn;
        
    }
    
    function updateRound($nRoundId = 0, $aParams = []) {
        
        $this->nRoundId = $nRoundId;
        
        $this->db->where('id', $this->nRoundId);
        $this->db->update('rounds', $aParams);
        
        $aReturn = [
            
            'success' => true
            
        ];
        
        return $aReturn;
        
    }
    
    function getRounds() {
        
        $aReturn = [];
        
        $aRounds = $this->db->get('rounds');
        
        foreach ($aRounds->result() as $k => $v) {
            
            $aTurns = $this->db->get_where('turns', [
                
                'round_id' => $v->id
                    
            ])->result();
            
            if ( is_array($aTurns) && count($aTurns) > 0 ) {
                
                $aReturn[$k] = [

                    'starter' => $v->starter,
                    'difficulty' => $v->difficulty,
                    'winner' => $v->winner,
                    'win_option' => $v->win_option,
                    'win_key' => $v->win_key,
                    'timestamp' => $v->timestamp,
                    'sDate' => mdate("%d/%m/%Y às %H:%i:%s", strtotime($v->timestamp)),
                    'turns' => $aTurns,
                    'bIsToday' => ( date('Y-m-d', strtotime($v->timestamp)) == date('Y-m-d') )

                ];
                
            }
            
        }
        
        return $aReturn;
        
    }
    
}
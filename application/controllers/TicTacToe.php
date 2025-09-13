<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TicTacToe extends CI_Controller {
    
    public function __construct() {
        
        parent::__construct();
        
        $this->load->database();
        
        $this->load->model('TicTacToe_Model');
        
    }

    public function index() {

        $this->load->view('tictactoe');

    }
    
    public function saveRound() {
        
        echo json_encode($this->TicTacToe_Model->saveRound());
        
    }

    public function saveTurn() {
        
        $aReturn = $this->TicTacToe_Model->saveTurn();
        
        $bHasWinner = ( in_array($this->input->post('sWinner'), [
            
            "H", "M"
            
        ]) );
        
        if ( $bHasWinner ) {
            
            $this->TicTacToe_Model->updateRound($this->input->post('nRoundId'), [
                
                'winner' => $this->input->post('sWinner'),
                'win_option' => $this->input->post('sWinOption'),
                'win_key' => $this->input->post('nWinKey')
                
            ]);
            
        }
        
        echo json_encode($aReturn);

    }
    
    public function updateRound() {

        $nRoundId = $this->input->post('nRoundId');

        $aParams = $this->input->post('aParams');

        $aReturn = $this->TicTacToe_Model->updateRound($nRoundId, $aParams);

        echo json_encode($aReturn);

    }
    
    public function getRounds() {
        
        $aReturn = $this->TicTacToe_Model->getRounds();
        
        $aReturn = array_reverse($aReturn);
        
        echo json_encode($aReturn);
        
    }

}

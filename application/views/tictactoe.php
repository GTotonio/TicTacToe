<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
    
    <head>
        
        <meta charset="utf-8">
        <title>TicTacToe - Gabriel Alves Totonio</title>
        
        <link rel="stylesheet" href="<?=base_url('assets/bootstrap/css/bootstrap.min.css')?>" />
        <link rel="stylesheet" href="<?=base_url('assets/fontawesome/css/all.css')?>" />

        <style type="text/css">
            /* BEGIN RESET */
            *, *:before, *:after {
                
                box-sizing: border-box;
                
            }
            
            html, body {
                
                margin: 0;
                padding: 0;
                
                width: 100%;
                height: 100%;
                
            }
            
            body {
                
                background: linear-gradient(rgba(0, 123, 255, 0.4), rgba(0, 98, 204, 0.47)), url(assets/images/tic-tac-toe-bg.jpg);
                
            }
            
            table {
                
                table-layout: fixed;
                
            }
            /* END RESET */
            
            /* BEGIN UTIL CLASSES STYLES */
            .w-100px { width: 100px; }
            .h-100px { height: 100px; }
            
            .o-y-auto { overflow-y: auto; }
            /* END UTIL CLASSES STYLES */
            
            /* BEGIN CUSTOM CLASSES STYLES */
            table.TicTacToe {
                
                width: 100%;
                text-align: center;
                
            }
            
            table.TicTacToe tr td {
                
                background-color: #FFFFFFF0;
                vertical-align: middle;
                font-size: 5rem;
                cursor: pointer;
                height: 8.5rem;
                
            }
            
            table.TicTacToe tr td:hover, table.TicTacToe tr td.active {
                
                background-color: #FFF;
                
            }
            
            .custom-bg {
                
                background-color: #000000D0;
            
            }

            .custom-bg.flex {
                
                flex: 1;

            }
            
            .custom-line {
                
                transform-origin: 0 100%;
                background: #000;
                
            }
            /* END CUSTOM CLASSES STYLES */
            
        </style>
        
        <script type="text/javascript" src="<?=base_url('assets/jquery/jquery-3.4.1.min.js')?>"></script>
        <script type="text/javascript" src="<?=base_url('assets/bootstrap/js/bootstrap.min.js')?>"></script>
        
        <script type="text/javascript">

            /**
             * Classe responsável pela criação do Game TicTacToe
             * 
             * @param {type} oConf
             * @returns {Boolean}
             */
            function TicTacToe(oConf) {

                // Contexto
                var _self = this;

                // Variável de configuração
                var _oConf = {

                    aValues: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    sDifficulty: "M",
                    sStarter: "H",
                    bViewOnly: false,
                    oViewParams: {}
                    
                };

                // Incrementando variável de configuração
                _oConf = $.extend(true, _oConf, oConf);

                // Informações dos jogadores
                var _oPlayersData = {

                    H: {
                        
                        sLabel: "Humano",
                        
                    },
                    M: {
                        
                        sLabel: "Máquina",
                        
                    }

                };
                
                // Informações dos caracteres
                var _oCharactersData = {
                    
                    X: {
                        
                        nScore: 0
                        
                    },
                    
                    O: {
                        
                        nScore: 0
                        
                    }
                    
                }

                // Dificuldades
                var _oAvailableDifficulties = {

                    E: {

                        nAverage: 40,
                        sLabel: "Fácil"

                    },
                    M: {

                        nAverage: 70,
                        sLabel: "Médio"

                    },
                    H: {

                        nAverage: 99,
                        sLabel: "Difícil"

                    }

                }

                // Combinações vitoriosas
                var _oWinOptions = {
                    
                    aDiagonals: [

                        [2, 4, 6],
                        [0, 4, 8]

                    ],

                    aLines: [

                        [0, 1, 2],
                        [3, 4, 5],
                        [6, 7, 8]

                    ],

                    aColumns: [

                        [0, 3, 6],
                        [1, 4, 7],
                        [2, 5, 8]

                    ]

                };
                
                var _nRoundId;
                
                var _aValues, _sDifficulty, _sStarter, _bViewOnly, _oViewParams;
                
                var _nTurn;
                var _elPlayInto, _elTable, _elXCounter, _elOCounter, _elXName, _elOName;
                
                var _sWinner, _oLineData;
                
                function _init() {
                    
                    // Prevenindo passagem de variável por referência
                    var oTempConf = $.extend(true, {}, _oConf);
                    
                    _setAValues(oTempConf.aValues);
                    
                    _setDifficulty(_getDifficulty() || oTempConf.sDifficulty);
                    
                    _setStarter(oTempConf.sStarter);
                    
                    _setTurn(1);
                    
                    _setViewOnly(oTempConf.bViewOnly);
                    _setViewParams(oTempConf.oViewParams);
                    
                    _setWinner(null);
                    _setLineData({});
                    
                    if ( !_getViewOnly() ) {
                    
                        $.ajax({

                            url: "<?=base_url();?>index.php/TicTacToe/saveRound",
                            type: "POST",
                            data: {
                                
                                sDifficulty: _getDifficulty()
                                
                            },
                            dataType: "JSON"

                        }).done(function(oData) {

                            _setRoundId(oData.id);

                        });
                        
                    }
                    
                }
                
                function _resetGame() {

                    if ( _getTurn() === 1 ) {
                        return false;
                    }
                    
                    _init();
                    
                    _self.playInto(_getElPlayInto());
                    
                    getTicTacToeHistory();
                    
                }
                
                function _runTurn(nIndex, sPlayer, elCharacters) {
                    
                    if ( _getWinner() ) {
                        
                        return true;
                        
                    }
                    
                    var elCharacter = $(elCharacters.get(nIndex)),
                        sCharacter = ( _getTurn() % 2 === 0 ? "O" : "X" ),
                        elIcon = $("<i />", {
                            
                            class: ( sCharacter === "O" ? "far fa-circle" : "fa fa-times" )
                            
                        });
                    
                    elCharacter.addClass("active").html(elIcon);
                    
                    _getAValues()[nIndex] = sPlayer;
                    
                    _checkMatchResult(sPlayer, sCharacter);
                    
                    var oLineData = _getLineData();
                    
                    $.ajax({
                        
                        url: "<?=base_url();?>index.php/TicTacToe/saveTurn",
                        type: "POST",
                        data: {
                            
                            nRoundId: _getRoundId(),
                            sCharacter: sCharacter,
                            nIndex: nIndex,
                            sWinner: _getWinner(),
                            sWinOption: oLineData.sLineChar,
                            nWinKey: oLineData.nLineDirection,
                            
                        },
                        dataType: "JSON"
                        
                    }).done(function(oData) {
                        
                        // console.log(oData);
                        
                    });
                    
                    _setTurn( _getTurn() + 1 );

                }

                function _getBestOption() {
                
                    /*
                     * Item 1:
                     * Verificar se existe opção em que, caso seja marcada, uma combinação vitoriosa seja construída.
                     */
                    
                    /*
                     * Item 2:
                     * Verificar se existe opção a marcar no intuito de evitar a derrota.
                     */
                    
                    /*
                     * Item 3:
                     * Verificar possibilidade de jogada bem sucedida (sem caractere adversário pertencente na provável combinação vitoriosa,
                     * e que utilize um caractere próprio já existente na tabela) priorizando meio, borda e cantos, nesta ordem.
                     */
                    
                    /*
                     * Item 4:
                     * Selecionar qualquer opção, priorizando meio, borda e cantos, nesta ordem.
                     */

                    function getPrefOption(aAvailableOptions) {
                        
                        var aWinOptions = _getWinOptions('aLines').concat(_getWinOptions('aDiagonals')).join().split(",").map(Number),
                            aPrefOptions = $(aWinOptions).filter(aAvailableOptions).toArray();
                        
                        var nPrefOption = aPrefOptions[Math.floor(Math.random() * aPrefOptions.length)];
                        
                        return nPrefOption;

                    }
                    
                    function checkChance() {
                        
                        var nChance = _getAvailableDifficulties(_getDifficulty())["nAverage"];
                        
                        return nChance >= ( Math.floor(Math.random() * 100) + 1 );
                        
                    }

                    var aAvailableOptions = _getAvailableOptions(_getAValues()),
                        aCPlayerSelectedOptions = _getPlayerSelectedOptions("M"),
                        aPPlayerSelectedOptions = _getPlayerSelectedOptions("H"),
                        aSelectedOptions = aCPlayerSelectedOptions.concat(aPPlayerSelectedOptions),
                        aBestOptions = [],
                        nBestOption = -1,
                        nOptionPriority = 0;

                    try {

                        $.each(_getWinOptions(), function (k, v) {

                            $.each(v, function (k2, v2) {

                                var aSelectedLineOptions = v2.filter(function (v3) {

                                    return (aSelectedOptions.indexOf(v3) !== -1);

                                });

                                var aSelectedComputerLineOptions = v2.filter(function (v3) {

                                    return (aCPlayerSelectedOptions.indexOf(v3) !== -1);

                                });

                                var aSelectedOpponentLineOptions = v2.filter(function (v3) {

                                    return (aPPlayerSelectedOptions.indexOf(v3) !== -1);

                                });

                                var aAvailableLineOptions = v2.filter(function (v3) {

                                    return aSelectedLineOptions.indexOf(v3) < 0;

                                });

                                // ********** ITEM 1:
                                // var aAvailableCOptions = $(v2).not(aCPlayerSelectedOptions).toArray(); // NÃO UTILIZADO NO MOMENTO.

                                if (checkChance() && aSelectedComputerLineOptions.length === 2 && aAvailableLineOptions.length === 1) {

                                    throw (nBestOption = aAvailableLineOptions[0]);

                                }
                                // ********** END ITEM 1

                                // ********** ITEM 2:
                                // var aAvailablePOptions = $(v2).not(aPPlayerSelectedOptions).toArray(); // NÃO UTILIZADO NO MOMENTO.

                                nBestOption = getPrefOption(aAvailableLineOptions);

                                if (checkChance() && aSelectedOpponentLineOptions.length === 2 && aAvailableLineOptions.length === 1) {

                                    nOptionPriority = 1;
                                    nBestOption = aAvailableLineOptions[0];

                                }
                                // ********** END ITEM 2

                                // ********** ITEM 3:
                                else if (checkChance() && !aSelectedOpponentLineOptions.length && aSelectedComputerLineOptions.length > 0) {

                                    nOptionPriority = 2;

                                }
                                // ********** END ITEM 3

                                // ********** ITEM 4:
                                else {

                                    nOptionPriority = 3;

                                }
                                // ********** END ITEM 4

                                if ( typeof aBestOptions[nOptionPriority] === "undefined" ) {

                                    aBestOptions[nOptionPriority] = [];

                                }

                                if ( nBestOption >= 0 ) {

                                    aBestOptions[nOptionPriority].push(nBestOption);

                                }

                            });

                        });
                        
                        aBestOptions = aBestOptions.filter(Boolean);
                        
                        nBestOption = aBestOptions[0][Math.floor(Math.random() * aBestOptions[0].length)];

                    } catch (e) {

                        // console.log(e);

                    } finally {

                        return nBestOption;

                    }

                }

                function _getAvailableOptions(aValues) {

                    return aValues.filter(function (v) {

                        return !(["H", "M"].includes(v));

                    });

                }

                function _getPlayerSelectedOptions(sPlayer) {

                    var aKeys = [],
                        aValues = _getAValues();

                    Object.keys(aValues).find(function (k) {

                        if (aValues[k] === sPlayer) {

                            aKeys.push(parseInt(k));

                        }

                    });

                    return aKeys;

                }
                
                function _checkMatchResult(sPlayer, sCharacter) {
                    
                    var aPlayerSelectedOptions = _getPlayerSelectedOptions(sPlayer),
                        sLineType,
                        nLineDirection;
                
                    var bPlayerWon = false;
                    
                    $.each(_getWinOptions(), function(k, v) {
                        
                        sLineType = k;
                        
                        $.each(v, function(k2, v2) {
                            
                            nLineDirection = k2;
                            
                            var aPlayerSelectedWinOptions = v2.filter(function (v3) {

                                return (aPlayerSelectedOptions.indexOf(v3) !== -1);

                            });
                            
                            if ( aPlayerSelectedWinOptions.length === 3 ) {
                                
                                return !( bPlayerWon = true );
                                
                            }
                            
                        });
                        
                        return !bPlayerWon;
                        
                    });
                    
                    if ( !bPlayerWon ) {
                        
                        if ( _getTurn() === 9 ) {
                            
                            setTimeout(function() {

                                alert("Oops... Houve um empate!");
                                
                                _resetGame();

                            }, 500);
                            
                        }
                        
                    } else {
                        
                        _setWinner( sCharacter === "X" ? _getStarter() : ( _getStarter() === "H" ? "M" : "H" ) );
                        
                        _updateCharacterScore(sCharacter, _getCharactersData(sCharacter).nScore + 1);
                            
                        _getElCounter(sCharacter).text(_getCharactersData(sCharacter).nScore);
                        
                        var sLineChar = "";

                        switch (sLineType) {
                            
                            case "aDiagonals":

                                sLineChar = "D";
                                break;

                            case "aLines":

                                sLineChar = "L";
                                break;

                            case "aColumns":

                                sLineChar = "C";
                                break;

                        }

                        _setLineData({

                            sLineChar: sLineChar,
                            sLineType: sLineType,
                            nLineDirection: nLineDirection

                        });

                        _drawLine(_getLineData());
                        
                        setTimeout(function() {

                            alert("O jogador " + _getPlayersData(sPlayer).sLabel + " venceu!");
                            
                            _resetGame();

                        }, 500);

                    }
                    
                }
                
                function _setAValues(aValues) {
                    
                    _aValues = aValues;
                    
                }
                
                function _getAValues() {
                    
                    return _aValues;
                    
                }
                
                function _getAvailableDifficulties(sDifficulty) {

                    if ( !sDifficulty ) {
                        
                        return _oAvailableDifficulties;
                        
                    }
                    
                    if ( _oAvailableDifficulties.hasOwnProperty(sDifficulty) ) {
                        
                        return _oAvailableDifficulties[sDifficulty];
                        
                    }

                }
                
                function _getWinOptions(sWinOption) {

                    if ( !sWinOption ) {
                        
                        return _oWinOptions;
                        
                    }
                    
                    if ( _oWinOptions.hasOwnProperty(sWinOption) ) {
                        
                        return _oWinOptions[sWinOption];
                        
                    }

                }
                
                function _setDifficulty(sDifficulty) {

                    _sDifficulty = sDifficulty;

                }
                
                function _getDifficulty() {
                
                    return _sDifficulty;
                
                }
                
                function _setStarter(sStarter) {
                    
                    _sStarter = sStarter;
                    
                }
                
                function _updateStarter(sStarter) {
                    
                    return $.ajax({
                        
                        url: "<?=base_url();?>index.php/TicTacToe/updateRound",
                        type: "POST",
                        data: {
                            
                            aParams: {

                                starter: sStarter

                            },
                            nRoundId: _getRoundId()
                            
                        },
                        dataType: "JSON"
                        
                    }).done(function(oData) {
                        
                        _setStarter(sStarter);
                        
                    });
                    
                }
                
                function _getStarter() {

                    return _sStarter;

                }
                
                function _setTurn(nTurn) {
                    
                    _nTurn = nTurn;
                    
                }
                
                function _getTurn() {
                    
                    return _nTurn;
                    
                }
                
                function _getPlayersData(sPlayer) {
                    
                    if ( !sPlayer ) {
                        
                        return _oPlayersData;
                        
                    }
                    
                    if ( _oPlayersData.hasOwnProperty(sPlayer) ) {
                        
                        return _oPlayersData[sPlayer];
                        
                    }
                    
                }
                
                function _getCharactersData(sCharacter) {
                    
                    if ( !sCharacter ) {
                        
                        return _oCharactersData;
                        
                    }
                    
                    if ( _oCharactersData.hasOwnProperty(sCharacter) ) {
                        
                        return _oCharactersData[sCharacter];
                        
                    }
                    
                }
                
                function _updateCharacterScore(sCharacter, nScore) {
                    
                    if ( _oCharactersData.hasOwnProperty(sCharacter) ) {
                        
                        _oCharactersData[sCharacter].nScore = nScore;
                        
                    }
                    
                }
                
                function _setWinner(sWinner) {
                    
                    _sWinner = sWinner;
                    
                }
                
                function _getWinner() {
                    
                    return _sWinner;
                    
                }
                
                function _setRoundId(nRoundId) {
                    
                    _nRoundId = nRoundId;
                    
                }
                
                function _getRoundId() {
                    
                    return _nRoundId;
                    
                }
                
                function _setLineData(oLineData) {
                    
                    _oLineData = oLineData;
                    
                }
                
                function _getLineData() {
                    
                    return _oLineData;
                    
                }
                
                function _setViewParams(oViewParams) {
                    
                    _oViewParams = oViewParams;
                    
                }
                
                function _getViewParams() {
                    
                    return _oViewParams;
                    
                }
                
                function _setViewOnly(bViewOnly) {
                    
                    _bViewOnly = bViewOnly;
                    
                }
                
                function _getViewOnly() {
                    
                    return _bViewOnly;
                    
                }
                
                function _genElTable(sClass) {
                    
                    _elTable = $("<table />", {
                        
                        html: "<tr> \
                                <td>&nbsp;</td> \
                                <td>&nbsp;</td> \
                                <td>&nbsp;</td> \
                            </tr> \
                            <tr> \
                                <td>&nbsp;</td> \
                                <td>&nbsp;</td> \
                                <td>&nbsp;</td> \
                            </tr> \
                            <tr> \
                                <td>&nbsp;</td> \
                                <td>&nbsp;</td> \
                                <td>&nbsp;</td> \
                            </tr>",
                        
                        class: "table table-bordered h-100 my-0" + ( !sClass ? '' : ' ' + sClass )
                        
                    });
                    
                    _getElTable().find("td").addClass("shadow-sm");
                    
                }
                
                function _getElTable() {
                    
                    return _elTable;
                    
                }
                
                function _setElPlayInto(elPlayInto) {
                    
                    _elPlayInto = elPlayInto;
                    
                }
                
                function _getElPlayInto() {
                    
                    return _elPlayInto;
                    
                }
                
                function _setElCounter(sChar, elCounter) {
                    
                    switch (sChar) {

                        case "X":

                            _elXCounter = elCounter;

                            break;

                        case "O":

                            _elOCounter = elCounter;

                            break;
                        
                    }
                    
                }
                
                function _getElCounter(sChar) {
                    
                    switch (sChar) {

                        case "X":

                            return _elXCounter;

                            break;

                        case "O":

                            return _elOCounter;

                            break;
                        
                    }
                    
                }
                
                function _setElName(sChar, elName) {
                    
                    switch (sChar) {

                        case "X":

                            _elXName = elName;

                            break;

                        case "O":

                            _elOName = elName;

                            break;
                        
                    }
                    
                }
                
                function _getElName(sChar) {
                    
                    switch (sChar) {

                        case "X":

                            return _elXName;

                            break;

                        case "O":

                            return _elOName;

                            break;
                        
                    }
                    
                }
                
                function _drawLine(oLineData) {
                    
                    var oLineParams = {

                            nStrokeHeight: 4

                        },
                        oTempLineParams = {};
                
                    var elTable = _getElTable();
                    
                    var nTableWidth = elTable.width()-2,
                        nTableHeight = elTable.height()-2;
                    
                    var sLineChar = oLineData.sLineChar,
                        nLineDirection = parseInt(oLineData.nLineDirection),
                        elSvg = $("<svg />", {

                            class: "position-absolute",
                            style: "width: " + nTableWidth + "px; height: " + nTableHeight + "px; top: 1px; left: 1px; z-index: 2"

                        });
                    
                    switch (sLineChar) {
                        
                        case "D":
                            
                            switch (nLineDirection) {

                                case 0:

                                    oTempLineParams = {
                                        
                                        x1: 0, y1: ( nTableHeight - oLineParams.nStrokeHeight ), x2: ( nTableWidth - oLineParams.nStrokeHeight ), y2: 0
                                        
                                    };

                                    break;

                                case 1:

                                    oTempLineParams = {
                                        
                                        x1: 0, y1: 0, x2: ( nTableWidth - oLineParams.nStrokeHeight ), y2: ( nTableHeight - oLineParams.nStrokeHeight )
                                        
                                    };

                                    break;
                                
                            }
                            
                            break;
                            
                        case "L":
                            
                            var nColHeight = nTableHeight / 3;
                            
                            switch(nLineDirection) {
                                
                                case 0:

                                    oTempLineParams = {
                                        
                                        x1: 0, y1: ( ( nColHeight / 2 ) - ( oLineParams.nStrokeHeight / 2 ) ),
                                        x2: nTableWidth, y2: ( ( nColHeight / 2 ) - ( oLineParams.nStrokeHeight / 2 ) ),
                                        
                                    };

                                    break;

                                case 1:

                                    oTempLineParams = {
                                        
                                        x1: 0, y1: ( ( nTableHeight / 2 ) + ( oLineParams.nStrokeHeight / 2 ) ),
                                        x2: nTableWidth, y2: ( ( nTableHeight / 2 ) + ( oLineParams.nStrokeHeight / 2 ) )
                                        
                                    };

                                    break;
                                    
                                case 2:

                                    oTempLineParams = {
                                        
                                        x1: 0, y1: ( nTableHeight - ( nColHeight / 2 ) - ( oLineParams.nStrokeHeight / 2 ) ),
                                        x2: nTableWidth, y2: ( nTableHeight - ( nColHeight / 2 ) - ( oLineParams.nStrokeHeight / 2 ) )
                                        
                                    };

                                    break;
                                
                            }
                            
                            break;

                        case "C":
                            
                            var nColWidth = nTableWidth / 3;
                            
                            switch(nLineDirection) {
                                
                                case 0:

                                    oTempLineParams = {
                                        
                                        x1: ( ( nColWidth / 2 ) - ( oLineParams.nStrokeHeight / 2 ) ), y1: 0,
                                        x2: ( ( nColWidth / 2 ) - ( oLineParams.nStrokeHeight / 2 ) ), y2: nTableWidth,
                                        
                                    };

                                    break;

                                case 1:

                                    oTempLineParams = {
                                        
                                        x1: ( nTableWidth / 2 - ( oLineParams.nStrokeHeight / 2 ) ), y1: 0,
                                        x2: ( nTableWidth / 2 - ( oLineParams.nStrokeHeight / 2 ) ), y2: nTableWidth
                                        
                                    };

                                    break;
                                    
                                case 2:

                                    oTempLineParams = {
                                        
                                        x1: ( nTableWidth - ( nColWidth / 2 ) - ( oLineParams.nStrokeHeight / 2 ) ), y1: 0,
                                        x2: ( nTableWidth - ( nColWidth / 2 ) - ( oLineParams.nStrokeHeight / 2 ) ), y2: nTableWidth
                                        
                                    };

                                    break;
                                
                            }
                            
                            break;
                        
                    }
                    
                    oLineParams = $.extend(oLineParams, oTempLineParams);
                    
                    var x1 = oLineParams.x1,
                        y1 = oLineParams.y1,
                        x2 = oLineParams.x2,
                        y2 = oLineParams.y2;
                    
                    var elLine = $("<div />", {
                        
                        class: "custom-line"
                        
                    }).css({
                        
                        height: oLineParams.nStrokeHeight + "px",
                        position: "absolute",
                        transform: "rotate(" + Math.atan2(y2 - y1, x2 - x1) * 180 / Math.PI + "deg)"
                        
                    }).width(Math.sqrt((x1-x2)*(x1-x2) + (y1-y2)*(y1-y2))).offset({left: x1, top: y1});
                    
                    elSvg.html(elLine).insertAfter(elTable);
                    
                }
                
                this.getAvailableDifficulties = function(sDifficulty) {
                    
                    if ( !sDifficulty ) {
                        
                        return _oAvailableDifficulties;
                        
                    }
                    
                    if ( _oAvailableDifficulties.hasOwnProperty(sDifficulty) ) {
                        
                        return _oAvailableDifficulties[sDifficulty];
                        
                    }
                    
                };
                
                this.playInto = function(el) {
                    
                    _setElPlayInto(el);
                    
                    _genElTable("TicTacToe");
                    
                    var elTable = _getElTable(),
                        elTds = elTable.find("td");
                    
                    var elTarget = _getElPlayInto().addClass("h-100 d-flex flex-column");
                    
                    _setElCounter("X", $("<span />").text(_getCharactersData("X").nScore));
                    _setElCounter("O", $("<span />").text(_getCharactersData("O").nScore));
                        
                    _setElName("X", $("<span />").text(_getPlayersData("H").sLabel));
                    _setElName("O", $("<span />").text(_getPlayersData("M").sLabel));
                
                    var elTopControls = $("<div />", {

                        class: "form-row"

                    });
                    
                    var oElsControls = {
                        
                        elDifficultySelect: $("<select />", {

                            class: "form-control",
                            html: "<option value='E'" + ( _getDifficulty() === "E" ? " selected" : "" ) + ">Fácil \
                            </option><option value='M'" +(  _getDifficulty() === "M" ? " selected" : "" ) + ">Médio \
                            </option><option value='H'" + ( _getDifficulty() === "H" ? " selected" : "" ) + ">Difícil \
                            </option>"

                        }),
                        
                        elXButton: $("<button />", {

                            type: "button",
                            class: "btn btn-primary active w-100",
                            html: $("<div />", {
                                
                                class: "d-flex align-items-center justify-content-between",
                                html: "<i class='fa fa-times'></i>"
                                
                            }).append(_getElName("X")).append(_getElCounter("X"))

                        }),
                        
                        elOButton: $("<button />", {

                            type: "button",
                            class: "btn btn-primary w-100",
                            html: $("<div />", {
                                
                                class: "d-flex align-items-center justify-content-between",
                                html: "<i class='far fa-circle'></i>"
                                
                            }).prepend(_getElName("O")).prepend(_getElCounter("O"))
                                    
                        }),

                        elResetButton: $("<button />", {

                            type: "button",
                            class: "btn btn-primary w-100",
                            html: $("<div />", {
                                
                                class: "d-flex align-items-center justify-content-between",
                                html: "<i class='fas fa-redo-alt'></i>"
                                
                            }).prepend("Reiniciar")
                                    
                        })
                    };
                    
                    $.each(oElsControls, function(k, v) {
                    
                        $("<div />", {
                            
                            class: "col-md-3 mb-2",
                            html: v
                            
                        }).appendTo(elTopControls);
                    
                    });
                            
                    elTds.click(function(e) {
                        
                        var $this = $(this),
                            nIndex = elTds.index(this);

                        if ($this.hasClass("active")) {

                            return false;

                        }
                        
                        _runTurn(nIndex, "H", elTds);
                        
                        if ( _getTurn() <= 9 ) {
                            
                            _runTurn(_getBestOption(), "M", elTds);
                            
                        }
                        
                    });
                    
                    oElsControls.elDifficultySelect.change(function() {
                        
                        _setDifficulty(this.value);
                        
                        _resetGame();
                        
                    });
                    
                    oElsControls.elXButton.click(function() {
                        
                        var $this = $(this);
                        
                        if ( $this.hasClass("active") || _getTurn() >= 1 ) {
                            
                            return false;
                            
                        }
                        
                        oElsControls.elOButton.removeClass("active");
                        
                        $this.addClass("active");
                        
                    });
                    
                    oElsControls.elOButton.click(function() {
                        
                        var $this = $(this);
                        
                        if ( $this.hasClass("active") || _getTurn() > 1 ) {
                            
                            return false;
                            
                        }
                        
                        if ( _getTurn() === 1 ) {
                            
                            _getElName("X").text(_getPlayersData("M").sLabel);
                            _getElName("O").text(_getPlayersData("H").sLabel);
                            
                            $.when(_updateStarter("M")).then(function(oData) {
                                
                                _runTurn(_getBestOption(), "M", elTds);
                                
                            });
                            
                        }
                        
                        oElsControls.elXButton.removeClass("active");
                        
                        $this.addClass("active");
                        
                    });

                    oElsControls.elResetButton.click(function() {
                        
                        _resetGame();
                        
                    });
                    
                    elTarget.empty().append(elTopControls, $("<div />", {
                        
                        class: "position-relative h-100"
                        
                    }).html(elTable));
                    
                }
                
                this.viewInto = function(el) {
                
                    _genElTable("h-100 table-sm text-center");
                    
                    var elTable = _getElTable(),
                        elTds = elTable.find("td");

                    $.each(_getAValues(), function(k, v) {
                        
                        var sIconClass = "fas fa-square invisible";
                        
                        if ( !Number.isInteger(v) ) {
                            
                            sIconClass = ( v === "X" ? "fa fa-times" : "far fa-circle" );
                            
                        }
                        
                        elTds.eq(k).html("<i class='" + sIconClass + "'></i>");
                        
                    });
                    
                    var oViewParams = _getViewParams();
                
                    el.html(elTable).promise().then(function() {
                        
                        _drawLine({

                            sLineChar: oViewParams.sWinOption,
                            nLineDirection: oViewParams.nWinKey

                        });
                        
                    });
                
                }
                    
                _init();

            }
            
            function getTicTacToeHistory() {
            
                return $.ajax({

                    url: "<?=base_url();?>index.php/TicTacToe/getRounds",
                    type: "POST",
                    dataType: "JSON"

                }).done(function(oData) {

                    var elTicTacToeHistory = $("#TicTacToeHistory").html("");
                    var elTicTacToeAllGames = $("#TicTacToeAllGames").text(oData.length);
                    var elTicTacToeAlert = $("#TicTacToeAlert");
                    var nMaxGamesPerDay = 30;
                    var nTodayGames = 0;

                    $.each(oData, function(k, v) {

                        nTodayGames += v.bIsToday ? 1 : 0;

                        var elGenTicTacToeView = $("<div />", {

                            class: "flex-auto d-none d-md-block w-100px"

                        });

                        var aValues = [];

                        $.each([...Array(9).keys()], function(v2) {

                            var nTurnPosition = v2,
                                sTurnChar = v2;

                            aValues[nTurnPosition] = aValues[nTurnPosition] || sTurnChar;

                            if ( typeof v.turns[v2] !== "undefined" ) {

                                var oTurn = v.turns[v2],
                                    nTurnPosition = oTurn.position,
                                    sTurnChar = oTurn.char;

                                aValues[nTurnPosition] = sTurnChar;

                            }

                        });

                        var ViewGame = new TicTacToe({

                            aValues: aValues,
                            sDifficulty: v["difficulty"],
                            bViewOnly: true,
                            oViewParams: {
                                
                                sWinOption: v["win_option"],
                                nWinKey: v["win_key"]
                                
                            }

                        });
                        
                        ViewGame.viewInto(elGenTicTacToeView);
                        
                        var oIconsClasses = {

                                H: ( v.starter === "H" ? "fa fa-times" : "far fa-circle" ),
                                M: ( v.starter === "M" ? "fa fa-times" : "far fa-circle" )

                            },
                            oTextClasses = {
                                
                                H: ( !v.winner ? " text-secondary" : ( v.winner === "H" ? " text-success" : " text-danger" ) ),
                                M: ( !v.winner ? " text-secondary" : ( v.winner === "M" ? " text-success" : " text-danger" ) )
                                
                            }

                        
                        $("<li />", {

                            class: "list-group-item px-2",
                            html: $("<div />", {

                                class: "card flex-md-row box-shadow",
                                html: "<div class='card-body d-flex flex-column align-items-start py-3'> \
                                    <div class='row'> \
                                        <div class='col-md-6'> \
                                            <small class='d-block" + oTextClasses.H + "'> \
                                                <span class='d-inline-block'>Humano -</span> <i class='" + oIconsClasses.H + "'></i> \
                                            </small> \
                                            <small class='d-block" + oTextClasses.M + "'> \
                                                <span class='d-inline-block'>Máquina -</span> <i class='" + oIconsClasses.M + "'></i> \
                                            </small> \
                                        </div> \
                                        <div class='col-md-6'> \
                                            <small> \
                                                <span class='d-inline-block'>Dificuldade: <b>" + ViewGame.getAvailableDifficulties(v["difficulty"])["sLabel"] + "</b></span> \
                                            </small> \
                                        </div> \
                                    </div> \
                                    <small class='text-muted mt-2'> \
                                        <i class='fa fa-clock'></i> " + v.sDate + " \
                                    </small> \
                                    </div> \
                                </div>"

                            }).prepend(elGenTicTacToeView)

                        }).appendTo(elTicTacToeHistory);

                    });

                    elTicTacToeAlert.toggleClass("d-none", nTodayGames < nMaxGamesPerDay);

                });
            
            }

            $(document).ready(function () {
            
                var Game = new TicTacToe({

                    aValues: [

                        0, 1, 2,
                        3, 4, 5,
                        6, 7, 8

                    ],
                    sDifficulty: "M",
                    sStarter: "H"

                }).playInto($("#TicTacToe"));
                
                getTicTacToeHistory();

            });

        </script>

    </head>
    
    <body>

        <main role="main" class="container-fluid h-100 py-3">
            
            <div class="row row-eq-height h-100">
                
                <div class="col-md-7">

                    <div class="d-flex flex-column h-100">
                        <div class="card custom-bg flex">

                            <div class="card-body p-3">
                                
                                <div id="TicTacToe"></div>

                            </div>

                        </div>

                        <div class="py-2">

                            <div class="card custom-bg">

                                <div class="card-body p-3">

                                    <div class="card">

                                        <div class="card-body">
                                            <p class="card-text mb-2 font-weight-bold">Desenvolvido por: Gabriel Alves Totonio</p>
                                            <div class="d-flex gap-3">
                                                <a href="https://www.linkedin.com/in/g-alvest" target="_blank" class="btn btn-outline-primary btn-sm" title="LinkedIn">
                                                    <i class="fab fa-linkedin"></i> LinkedIn
                                                </a>
                                                <a href="https://github.com/GTotonio" target="_blank" class="btn btn-outline-dark btn-sm ml-1" title="GitHub">
                                                    <i class="fab fa-github"></i> GitHub
                                                </a>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                    
                </div>
                
                <div class="col-md-5">
                    
                    <div class="position-relative h-100">
                        
                        <div class="position-absolute w-100 h-100 pb-2">
                            
                            <div class="card custom-bg h-100">

                                <div class="card-body h-100 p-3">

                                    <div class="card h-100">

                                        <div class="card-header">

                                            <span>Últimos jogos - <span id="TicTacToeAllGames"></span></span>

                                            <div id="TicTacToeAlert" class="d-none alert alert-warning alert-dismissible fade show mt-2 mb-0 small" role="alert">
                                                <strong>Ei!</strong> Eu acho que você já jogou demais hoje. Que tal descansar um pouco? 🙂
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                        </div>

                                        <ul id="TicTacToeHistory" class="list-group list-group-flush h-100 o-y-auto"></ul>

                                    </div>

                                </div>

                            </div>
                            
                        </div>

                        <div class="clearfix"></div>
                        
                    </div>
                    
                </div>
                
            </div>
            
        </main>

    </body>
    
</html>
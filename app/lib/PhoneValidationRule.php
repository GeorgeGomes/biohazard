<?php

class PhoneValidationRule extends \Illuminate\Validation\Validator {
	
	public function validatePhone($attribute, $value, $parameters) {
		return preg_match("/^([0-9\+]*)$/", $value);
	}

	public function validateCpf($attribute, $value, $parameters) {	 
	    // Verifica se um número foi informado
	    if(empty($value)) {
	        return false;
	    }
	 
	    // Elimina possivel mascara
	    $value = preg_replace( '/[^0-9]/is', '', $value );
	    $value = str_pad($value, 11, '0', STR_PAD_LEFT);
	     
	    // Verifica se o numero de digitos informados é igual a 11 
	    if (strlen($value) != 11) {
	        return false;
	    }
	    // Verifica se nenhuma das sequências invalidas abaixo 
	    // foi digitada. Caso afirmativo, retorna falso
	    else if ($value == '00000000000' || 
	        $value == '11111111111' || 
	        $value == '22222222222' || 
	        $value == '33333333333' || 
	        $value == '44444444444' || 
	        $value == '55555555555' || 
	        $value == '66666666666' || 
	        $value == '77777777777' || 
	        $value == '88888888888' || 
	        $value == '99999999999') {
	        return false;
	     // Calcula os digitos verificadores para verificar se o
	     // CPF é válido
	     }
	     else {   
	         
	        for ($t = 9; $t < 11; $t++) {
	             
	            for ($d = 0, $c = 0; $c < $t; $c++) {
	                $d += $value{$c} * (($t + 1) - $c);
	            }
	            $d = ((10 * $d) % 11) % 10;
	            if ($value{$c} != $d) {
	                return false;
	            }
	        }
	    }
 
        return true;
	}

	public function validateCnpj($attribute, $value, $parameters){
	 	// Deixa o CNPJ com apenas números
	    $value = preg_replace( '/[^0-9]/', '', $value );
	    
	    // Garante que o CNPJ é uma string
	    $value = (string)$value;
	    
	    // O valor original
	    $cnpj_original = $value;
	    
	    // Captura os primeiros 12 números do CNPJ
	    $primeiros_numeros_cnpj = substr( $value, 0, 12 );
	    
	    /**
	     * Multiplicação do CNPJ
	     *
	     * @param string $cnpj Os digitos do CNPJ
	     * @param int $posicoes A posição que vai iniciar a regressão
	     * @return int O
	     *
	     */
	    if ( ! function_exists('multiplica_cnpj') ) {
	        function multiplica_cnpj( $cnpj, $posicao = 5 ) {
	            // Variável para o cálculo
	            $calculo = 0;
	            
	            // Laço para percorrer os item do cnpj
	            for ( $i = 0; $i < strlen( $cnpj ); $i++ ) {
	                // Cálculo mais posição do CNPJ * a posição
	                $calculo = $calculo + ( $cnpj[$i] * $posicao );
	                
	                // Decrementa a posição a cada volta do laço
	                $posicao--;
	                
	                // Se a posição for menor que 2, ela se torna 9
	                if ( $posicao < 2 ) {
	                    $posicao = 9;
	                }
	            }
	            // Retorna o cálculo
	            return $calculo;
	        }
	    }
	    
	    // Faz o primeiro cálculo
	    $primeiro_calculo = multiplica_cnpj( $primeiros_numeros_cnpj );
	    
	    // Se o resto da divisão entre o primeiro cálculo e 11 for menor que 2, o primeiro
	    // Dígito é zero (0), caso contrário é 11 - o resto da divisão entre o cálculo e 11
	    $primeiro_digito = ( $primeiro_calculo % 11 ) < 2 ? 0 :  11 - ( $primeiro_calculo % 11 );
	    
	    // Concatena o primeiro dígito nos 12 primeiros números do CNPJ
	    // Agora temos 13 números aqui
	    $primeiros_numeros_cnpj .= $primeiro_digito;
	 
	    // O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
	    $segundo_calculo = multiplica_cnpj( $primeiros_numeros_cnpj, 6 );
	    $segundo_digito = ( $segundo_calculo % 11 ) < 2 ? 0 :  11 - ( $segundo_calculo % 11 );
	    
	    // Concatena o segundo dígito ao CNPJ
	    $value = $primeiros_numeros_cnpj . $segundo_digito;
	    
	    // Verifica se o CNPJ gerado é idêntico ao enviado
	    if ( $value === $cnpj_original ) {
	        return true;
	    }
	}

}
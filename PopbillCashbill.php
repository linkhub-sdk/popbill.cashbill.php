<?php
/**
* =====================================================================================
* Class for base module for Popbill API SDK. It include base functionality for
* RESTful web service request and parse json result. It uses Linkhub module
* to accomplish authentication APIs.
*
* This module uses curl and openssl for HTTPS Request. So related modules must
* be installed and enabled.
*
* http://www.linkhub.co.kr
* Author : Kim Seongjun (pallet027@gmail.com)
* Written : 2014-09-04
*
* Thanks for your interest.
* We welcome any suggestions, feedbacks, blames or anything.
* ======================================================================================
*/
require_once 'Popbill/popbill.php';

class CashbillService extends PopbillBase {
	
	public function __construct($LinkID,$SecretKey) {
    	parent::__construct($LinkID,$SecretKey);
    	$this->AddScope('140');
    }
    
    //팝빌 현금영수증 연결 url
    public function GetURL($CorpNum,$UserID,$TOGO) {
    	$response = $this->executeCURL('/Cashbill/?TG='.$TOGO,$CorpNum,$UserID);
    	return $response->url;
    }
    
    //관리번호 사용여부 확인
    public function CheckMgtKeyInUse($CorpNum,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	try
    	{
    		$response = $this->executeCURL('/Cashbill/'.$MgtKey,$CorpNum);
    		return is_null($response->itemKey) == false;
    	}catch(PopbillException $pe) {
    		if($pe->getCode() == -14000005) {return false;}
    		throw $pe;
    	}
    }
    
    //임시저장
    public function Register($CorpNum, $Cashbill, $UserID = null) {
    	$postdata = json_encode($Cashbill);
    	return $this->executeCURL('/Cashbill',$CorpNum,$UserID,true,null,$postdata);
    }    
    
    //삭제
    public function Delete($CorpNum,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'DELETE','');
    }
    
    //수정
    public function Update($CorpNum,$MgtKey,$Cashbill, $UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}

    	$postdata = json_encode($Cashbill);
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true, 'PATCH', $postdata);
    }
    
    //발행
    public function Issue($CorpNum,$MgtKey,$Memo = '', $UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	$Request = new IssueRequest();
    	$Request->memo = $Memo;
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'ISSUE',$postdata);
    }
    
    //발행취소
    public function CancelIssue($CorpNum,$MgtKey,$Memo = '',$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	$Request = new MemoRequest();
    	$Request->memo = $Memo;
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'CANCELISSUE',$postdata);
    }
    
    //알림메일 재전송
    public function SendEmail($CorpNum,$MgtKey,$Receiver,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	$Request = array('receiver' => $Receiver);
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'EMAIL',$postdata);
    }
    
    //알림문자 재전송
    public function SendSMS($CorpNum,$MgtKey,$Sender,$Receiver,$Contents,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	$Request = array('receiver' => $Receiver,'sender'=>$Sender,'contents' => $Contents);
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'SMS',$postdata);
    }
    
    //알림팩스 재전송
    public function SendFAX($CorpNum,$MgtKey,$Sender,$Receiver,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.',-99999999);
    	}
    	
    	$Request = array('receiver' => $Receiver,'sender'=>$Sender);
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'FAX',$postdata);
    }
    
    //현금영수증 요약정보 및 상태정보 확인
    public function GetInfo($CorpNum,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum);
    }
    
    //현금영수증 상세정보 확인 
    public function GetDetailInfo($CorpNum,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	return $this->executeCURL('/Cashbill/'.$MgtKey.'?Detail', $CorpNum);
    }
    
    //현금영수증 요약정보 다량확인 최대 1000건
    public function GetInfos($CorpNum,$MgtKeyList = array()) {
    	if(is_null($MgtKeyList) || empty($MgtKeyList)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	$postdata = json_encode($MgtKeyList);
    	
    	return $this->executeCURL('/Cashbill/States', $CorpNum, null, true,null,$postdata);
    }
    
    //현금영수증 문서이력 확인 
    public function GetLogs($CorpNum,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	return $this->executeCURL('/Cashbill/'.$MgtKey.'/Logs', $CorpNum);
    }
    
    //팝업URL
    public function GetPopUpURL($CorpNum,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey.'?TG=POPUP', $CorpNum,$UserID)->url;
    }
    
    //인쇄URL
    public function GetPrintURL($CorpNum,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey.'?TG=PRINT', $CorpNum,$UserID)->url;
    }

    //공급받는자 인쇄URL
    public function GetEPrintURL($CorpNum,$MgtKey,$UserID = null) {
        if(is_null($MgtKey) || empty($MgtKey)) {
            throw new PopbillException('관리번호가 입력되지 않았습니다.');
        }
        
        return $this->executeCURL('/Cashbill/'.$MgtKey.'?TG=EPRINT', $CorpNum,$UserID)->url;
    }
    
    //공급받는자 메일URL
    public function GetMailURL($CorpNum,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey.'?TG=MAIL', $CorpNum,$UserID)->url;
    }
    
    //현금영수증 다량인쇄 URL
    public function GetMassPrintURL($CorpNum,$MgtKeyList = array(),$UserID = null) {
    	if(is_null($MgtKeyList) || empty($MgtKeyList)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	$postdata = json_encode($MgtKeyList);
    	
    	return $this->executeCURL('/Cashbill/Prints', $CorpNum, $UserID, true,null,$postdata)->url;
    }
    
    //발행단가 확인
    public function GetUnitCost($CorpNum) {
    	return $this->executeCURL('/Cashbill?cfg=UNITCOST', $CorpNum)->unitCost;
    }
        
}

class Cashbill
{
	public $mgtKey;
    public $tradeDate;
    public $tradeUsage;
    public $tradeType;
    
    public $taxationType;
    public $supplyCost;
    public $tax;
    public $serviceFee;
    public $totalAmount;
    
    public $franchiseCorpNum;
    public $franchiseCorpName;
    public $franchiseCEOName;
    public $franchiseAddr;
    public $franchiseTEL;
    
    public $identityNum;
    public $customerName;
    public $itemName;
    public $orderNumber;
    
    public $email;
    public $hp;
    public $fax;
    public $smssendYN;
    public $faxsendYN;
    
    public $orgConfirmNum;
    
}
class MemoRequest {
	public $memo;
}
class IssueRequest {
	public $memo;
}
?>
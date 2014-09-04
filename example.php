<?php

require_once 'PopbillCashbill.php';

$LinkID = 'TESTER';
$SecretKey = 'huf38wRpmUUdJuHAEXaeTgBbLE8SLUNPERxW3Fy7mL8=';


$CashbillService = new CashbillService($LinkID,$SecretKey);

$CashbillService->IsTest(true);

try {
	echo $CashbillService->GetUnitCost('1231212312');
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}
echo chr(10);

$Cashbill = new Cashbill();

$Cashbill->mgtKey = '123123';
$Cashbill->tradeType = '승인거래'; // 승인거래 or 취소거래
$Cashbill->franchiseCorpNum = '1231212312';
$Cashbill->franchiseCorpName = '발행자 상호';
$Cashbill->franchiseCEOName = '발행자 대표자명';
$Cashbill->franchiseAddr = '발행자 주소';
$Cashbill->franchiseTEL = '070-1234-1234';
$Cashbill->identityNum = '01041680206';
$Cashbill->customerName = '고객명';
$Cashbill->itemName = '상품명';
$Cashbill->orderNumber = '주문번호';
$Cashbill->email = 'test@test.com';
$Cashbill->hp = '111-1234-1234';
$Cashbill->fax = '777-444-3333';
$Cashbill->serviceFee = '0';
$Cashbill->supplyCost = '10000';
$Cashbill->tax = '1000';
$Cashbill->totalAmount = '11000';
$Cashbill->tradeUsage = '소득공제용'; //소득공제용 or 지출증빙용
$Cashbill->taxationType = '과세'; // 과세 or 비과세

$Cashbill->smssendYN = false;
$Cashbill->faxsendYN = false;

try {
	$result = $CashbillService->Register('1231212312',$Cashbill,null);
	echo $result->message;
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

echo chr(10);

try {
	$result = $CashbillService->Update('1231212312','123123',$Cashbill,null,false);
	echo $result->message;
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

echo chr(10);

try {
	$result = $CashbillService->GetDetailInfo('1231212312','123123');
	var_dump($result);
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

echo chr(10);

try {
	$result = $CashbillService->Issue('1231212312','123123','발행 메모');
	echo $result->message;
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

echo chr(10);

try {
	$result = $CashbillService->GetInfo('1231212312','123123');
	var_dump($result);
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

echo chr(10);

try {
	$result = $CashbillService->SendEmail('1231212312','123123','pallet027@gmail.com');
	echo $result->message;
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

echo chr(10);

try {
	$result = $CashbillService->GetLogs('1231212312','123123');
	var_dump($result);
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

echo chr(10);

 
$MgtKeyList = array(
                 '123123',
                 '123123123',
                 '123123123123',
                 '1',
                 '2'
 );

try {
	$result = $CashbillService->GetMassPrintURL('1231212312',$MgtKeyList,'hklee0002');
	var_dump($result);
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

try {
	$result = $CashbillService->GetInfos('1231212312',$MgtKeyList);
	var_dump($result);
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

echo chr(10);

try {
	$result = $CashbillService->CancelIssue('1231212312','123123','발행취소 메모');
	echo $result->message;
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

echo chr(10);


try {
	$result = $CashbillService->Delete('1231212312','123123');
	echo $result->message;
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

echo chr(10);


?>

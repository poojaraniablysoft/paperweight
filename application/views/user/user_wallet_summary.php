<?php
$wallet = new Wallet;


echo Utilities::getLabel( 'L_Wallet_balance' ) . ': ' . $wallet->getUserWalletBalance() . '<br />';

echo Utilities::getLabel( 'L_Incoming_amount' ) . ': ' . $wallet->getIncomingAmount() . '<br />';

echo Utilities::getLabel( 'L_Liabilities' ) . ': ' . $wallet->liabilities() . '<br />';

echo Utilities::getLabel( 'L_Available_balance' ) . ': ' . $wallet->getAvailableBalance() . '<br />';
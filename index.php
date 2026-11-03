<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
start_app_session();

$MODULES = [
 'crops'=>['label'=>'Crop Farming','icon'=>'🌱','fields'=>['venture'=>['Business venture','select','required'],
  'crop_name'=>['Crop name','text','required'],'variety'=>['Variety','text'],'field_name'=>['Field','text'],
  'planting_date'=>['Planting date','date'],'expected_harvest_date'=>['Expected harvest','date'],
  'area'=>['Area','number'],'unit'=>['Unit','text'],'quantity'=>['Quantity','number'],'status'=>['Status','text'],'notes'=>['Notes','textarea']]],
 'harvests'=>['label'=>'Harvests','icon'=>'🌾','fields'=>['venture'=>['Business venture','select','required'],
  'crop_name'=>['Crop name','text','required'],'harvest_date'=>['Harvest date','date'],'quantity'=>['Quantity','number','required'],
  'unit'=>['Unit','text'],'field_name'=>['Field','text'],'quality'=>['Quality','text'],'notes'=>['Notes','textarea']]],
 'livestock'=>['label'=>'Livestock','icon'=>'🐄','fields'=>['venture'=>['Business venture','select','required'],
  'animal_id'=>['Animal ID','text'],'animal_type'=>['Animal type','text','required'],'breed'=>['Breed','text'],'gender'=>['Gender','text'],
  'birth_date'=>['Birth date','date'],'weight'=>['Weight','number'],'location'=>['Location','text'],'purchase_cost'=>['Purchase cost','number'],
  'current_value'=>['Current value','number'],'health_status'=>['Health status','text'],'vaccination'=>['Vaccination','text'],'supplier'=>['Supplier','text'],'notes'=>['Notes','textarea']]],
 'veterinary'=>['label'=>'Veterinary','icon'=>'🩺','fields'=>['venture'=>['Business venture','select','required'],
  'animal_id'=>['Animal ID','text'],'animal_type'=>['Animal type','text'],'treatment_date'=>['Treatment date','date'],
  'medicine'=>['Medicine','text'],'dosage'=>['Dosage','text'],'next_due_date'=>['Next due date','date'],'cost'=>['Cost','number'],'notes'=>['Notes','textarea']]],
 'inventory'=>['label'=>'Inventory & Shop Products','icon'=>'📦','fields'=>['venture'=>['Business venture','select','required'],
  'item_name'=>['Item name','text','required'],'category'=>['Category','text'],'quantity'=>['Quantity','number','required'],
  'unit'=>['Unit','text','required'],'unit_price'=>['Unit price','number'],'supplier_name'=>['Supplier','text'],
  'minimum_stock'=>['Minimum stock','number'],'notes'=>['Notes','textarea']]],
 'procurement'=>['label'=>'Procurement','icon'=>'🛒','fields'=>['venture'=>['Business venture','select','required'],
  'item_name'=>['Item name','text','required'],'quantity'=>['Quantity','number','required'],'unit'=>['Unit','text','required'],
  'unit_cost'=>['Unit cost','number','required'],'supplier'=>['Supplier','text'],'purchase_date'=>['Purchase date','date','required'],'reference'=>['Reference','text'],'notes'=>['Notes','textarea']]],
 'sales'=>['label'=>'Sales','icon'=>'💰','fields'=>['venture'=>['Business venture','select','required'],
  'item_name'=>['Item name','text','required'],'quantity'=>['Quantity','number','required'],'unit'=>['Unit','text','required'],
  'unit_price'=>['Selling price/unit','number','required'],'total_price'=>['Total price','number'],'customer'=>['Customer','text'],'sale_date'=>['Sale date','date','required'],'reference'=>['Reference','text'],'notes'=>['Notes','textarea']]],
 'finance'=>['label'=>'Group Finance','icon'=>'💳','fields'=>['venture'=>['Business venture','select','required'],
  'transaction_type'=>['Transaction type','select','required'],'description'=>['Description','text','required'],'amount'=>['Amount','number','required'],
  'transaction_date'=>['Date','date','required'],'category'=>['Category','text'],'reference'=>['Reference','text'],'notes'=>['Notes','textarea']]],
 'advisory'=>['label'=>'Advisory & Agribusiness Services','icon'=>'🤝','fields'=>['venture'=>['Business venture','select','required'],
  'client_name'=>['Client / organisation','text','required'],'phone'=>['Client phone','text'],'email'=>['Client email','email'],'service_type'=>['Service category','select','required'],
  'engagement_title'=>['Engagement / project title','text','required'],'scope_of_work'=>['Scope of work / client request','textarea','required'],'assigned_to'=>['Assigned consultant / employee','text'],
  'start_date'=>['Start date','date'],'due_date'=>['Target completion date','date'],'status'=>['Engagement status','select','required'],
  'fee_amount'=>['Agreed fee (MWK)','number'],'invoice_reference'=>['Invoice reference','text'],'amount_paid'=>['Amount paid (MWK)','number'],
  'project_cost'=>['Direct project costs (MWK)','number'],'completion_notes'=>['Recommendations / outcome','textarea'],'notes'=>['Additional notes','textarea']]],
 'employees'=>['label'=>'Employees','icon'=>'👥','fields'=>['venture'=>['Business venture','select','required'],
  'employee_name'=>['Employee name','text','required'],'job_title'=>['Job title','text'],'phone'=>['Phone','text'],'venture'=>['Assigned venture','select','required'],'date_joined'=>['Date joined','date'],'years_worked'=>['Years worked','number'],'salary'=>['Salary (MWK)','number'],'salary_frequency'=>['Salary frequency','select'],'salary_payment_amount'=>['Salary payment amount (MWK)','number'],'salary_payment_date'=>['Salary payment date','date'],'status'=>['Employment status','text'],'notes'=>['Notes','textarea']]],
 'equipment'=>['label'=>'Equipment','icon'=>'🚜','fields'=>['venture'=>['Business venture','select','required'],
  'equipment_name'=>['Equipment name','text','required'],'category'=>['Category','text'],'quantity'=>['Quantity','number'],'condition'=>['Condition','text'],'location'=>['Location','text'],'purchase_date'=>['Purchase date','date'],'value'=>['Value','number'],'notes'=>['Notes','textarea']]],
 'fields'=>['label'=>'Field Mapping','icon'=>'🗺️','fields'=>['venture'=>['Business venture','select','required'],
  'field_name'=>['Field name','text','required'],'area'=>['Area','number'],'unit'=>['Area unit','text'],'crop'=>['Current crop','text'],'location'=>['Location / GPS','text'],'status'=>['Status','text'],'notes'=>['Notes','textarea']]],
 'customers'=>['label'=>'Customers','icon'=>'👤','fields'=>['venture'=>['Business venture','select','required'],
  'name'=>['Customer name','text','required'],'phone'=>['Phone','text'],'email'=>['Email','email'],'address'=>['Address','text'],'notes'=>['Notes','textarea']]],
 'suppliers'=>['label'=>'Suppliers','icon'=>'🏢','fields'=>['venture'=>['Business venture','select','required'],
  'name'=>['Supplier name','text','required'],'phone'=>['Phone','text'],'email'=>['Email','email'],'address'=>['Address','text'],'notes'=>['Notes','textarea']]],
];

function module_label(string $m, array $MODULES): string { return $MODULES[$m]['label'] ?? ucfirst($m); }
function clean_decimal(mixed $v): float {
    if (is_int($v) || is_float($v)) return (float)$v;
    if (is_string($v)) {
        $v = trim(str_replace([',','MWK','mwk',' '], '', $v));
        return is_numeric($v) ? (float)$v : 0.0;
    }
    return 0.0;
}
function sale_total(array $d): float {
    return clean_decimal($d['quantity'] ?? 0) * clean_decimal($d['unit_price'] ?? 0);
}
function procurement_total(array $d): float {
    return clean_decimal($d['quantity'] ?? 0) * clean_decimal($d['unit_cost'] ?? 0);
}
function record_title(string $module,array $d): string {
    foreach(['item_name','crop_name','animal_type','animal_id','employee_name','equipment_name','field_name','name','description'] as $k) if (!empty($d[$k])) return (string)$d[$k];
    return 'Record';
}
function get_records(PDO $pdo,string $module): array {
    $s=$pdo->prepare("SELECT record_uuid,data_json,created_at,updated_at FROM records WHERE module=? ORDER BY updated_at DESC,id DESC");
    $s->execute([$module]); $out=[];
    foreach($s as $r){$d=json_decode($r['data_json'],true); if(!is_array($d))$d=[]; $d['_uuid']=$r['record_uuid']; $d['_created_at']=$r['created_at']; $d['_updated_at']=$r['updated_at']; $out[]=$d;}
    return $out;
}
function find_record(PDO $pdo,string $uuid): ?array {
    $s=$pdo->prepare("SELECT * FROM records WHERE record_uuid=? LIMIT 1");$s->execute([$uuid]);$r=$s->fetch();
    if(!$r)return null;$d=json_decode($r['data_json'],true);return ['row'=>$r,'data'=>is_array($d)?$d:[]];
}
function canonical_venture(mixed $venture, string $module=''): string {
    $v = strtolower(trim((string)$venture));
    $aliases = ['shop'=>'shop','store'=>'shop','retail'=>'shop','advisory'=>'shop','services'=>'shop',
      'crop'=>'crops','farm'=>'crops','beans'=>'crops','onions'=>'crops','beans & onions farm'=>'crops','crop farming'=>'crops','crops'=>'crops',
      'cattle farm'=>'cattle','livestock'=>'cattle','cow'=>'cattle','cattle'=>'cattle'];
    if ($module === 'advisory') return 'shop';
    if (isset($aliases[$v])) return $aliases[$v];
    if ($v !== '') return $v;
    if (in_array($module, ['livestock','veterinary'], true)) return 'cattle';
    if (in_array($module, ['crops','harvests','fields'], true)) return 'crops';
    return 'shop';
}
function normalise_item_name(string $name): string {
    $name = trim(preg_replace('/\s+/u', ' ', $name) ?? $name);
    return function_exists('mb_strtolower') ? mb_strtolower($name, 'UTF-8') : strtolower($name);
}
function stock_key(string $name,string $unit,string $venture='shop'): string { return canonical_venture($venture).'|'.normalise_item_name($name).'|'.normalise_item_name($unit); }
function inventory_rows(PDO $pdo): array { return get_records($pdo,'inventory'); }
function adjust_stock(PDO $pdo,string $item,float $delta,string $unit,string $refModule,string $refUuid,int $uid,string $note='',string $venture='shop'): void {
    if ($item==='') return;
    $rows=inventory_rows($pdo); $found=null;
    foreach($rows as $r){ if(stock_key((string)($r['item_name']??''),(string)($r['unit']??''),(string)($r['venture']??'shop'))===stock_key($item,$unit,$venture)){ $found=$r; break; } }
    if(!$found){
        if($delta<0) throw new RuntimeException('Stock item "'.$item.'" does not exist. Add it to Inventory before recording a sale.');
        if($delta===0) return;
        $new=['venture'=>$venture,'item_name'=>$item,'quantity'=>$delta,'unit'=>$unit,'category'=>'Auto-created from '.$refModule,'unit_price'=>0,'minimum_stock'=>0,'supplier_name'=>'','notes'=>'Created automatically by stock movement'];
        save_record_internal($pdo,'inventory',$new,null,$uid);
        $invUuid=$new['_uuid'];
    } else {
        $new=$found; $current=clean_decimal($found['quantity']??0);
        if($delta<0 && $current+$delta<0) throw new RuntimeException('Insufficient stock for "'.$item.'". Available: '.$current.' '.$unit.'.');
        $new['quantity']=max(0,$current+$delta); $invUuid=$found['_uuid'];
        save_record_internal($pdo,'inventory',$new,$invUuid,$uid);
    }
    $type=$delta>=0?'IN':'OUT';
    $s=$pdo->prepare("INSERT INTO stock_ledger(item_key,item_name,movement_type,quantity,reference_module,reference_uuid,notes,user_id) VALUES(?,?,?,?,?,?,?,?)");
    $s->execute([stock_key($item,$unit,$venture),$item,$type,abs($delta),$refModule,$refUuid,$note,$uid]);
}
function save_record_internal(PDO $pdo,string $module,array $data,?string $uuid,int $uid): string {
    unset($data['_uuid'],$data['_created_at'],$data['_updated_at']);
    $uuid=$uuid ?: sprintf('%s-%s-%s-%s-%s',bin2hex(random_bytes(4)),bin2hex(random_bytes(2)),bin2hex(random_bytes(2)),bin2hex(random_bytes(2)),bin2hex(random_bytes(6)));
    $json=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $title=record_title($module,$data);
    if($uuid){
        $existing=$pdo->prepare("SELECT id FROM records WHERE record_uuid=?");$existing->execute([$uuid]);
        if($existing->fetch()){
            $s=$pdo->prepare("UPDATE records SET title=?,data_json=?,updated_by=?,updated_at=CURRENT_TIMESTAMP WHERE record_uuid=?");
            $s->execute([$title,$json,$uid,$uuid]);
            return $uuid;
        }
    }
    $s=$pdo->prepare("INSERT INTO records(record_uuid,module,title,data_json,created_by,updated_by) VALUES(?,?,?,?,?,?)");
    $s->execute([$uuid,$module,$title,$json,$uid,$uid]); return $uuid;
}
function audit(PDO $pdo,int $uid,string $action,string $module,?string $uuid,array $details=[]):void{
    $s=$pdo->prepare("INSERT INTO audit_log(user_id,action_name,module,record_uuid,details_json) VALUES(?,?,?,?,?)");
    $s->execute([$uid,$action,$module,$uuid,json_encode($details)]);
}

function ai_advisor_insights(PDO $pdo): array {
    // Transparent rule-based farm intelligence. Findings are derived from saved MySQL records.
    $insights=[]; $today=new DateTimeImmutable('today');
    $inventory=get_records($pdo,'inventory'); $crops=get_records($pdo,'crops');
    $animals=get_records($pdo,'livestock'); $vet=get_records($pdo,'veterinary');
    $sales=get_records($pdo,'sales'); $procurement=get_records($pdo,'procurement');
    $revenue=0.0; foreach($sales as $r)$revenue+=sale_total($r);
    $expenses=0.0; foreach($procurement as $r)$expenses+=procurement_total($r);
    if(!$sales && !$procurement) $insights[]=['level'=>'info','icon'=>'📊','title'=>'Build your financial baseline','detail'=>'No sales or procurement records are available for a meaningful financial comparison yet.','action'=>'Record sales and purchases to unlock trend and margin insights.','link'=>'sales'];
    elseif($expenses>$revenue) $insights[]=['level'=>'high','icon'=>'⚠️','title'=>'Procurement is higher than sales revenue','detail'=>'Recorded procurement exceeds recorded sales by MWK '.number_format($expenses-$revenue,2).'. This is a comparison of recorded activity, not a full accounting profit figure.','action'=>'Review recent purchases, sales prices and upcoming cash requirements.','link'=>'reports'];
    elseif($revenue>0) $insights[]=['level'=>'good','icon'=>'📈','title'=>'Sales cover recorded procurement','detail'=>'Sales revenue is MWK '.number_format($revenue,2).' and procurement is MWK '.number_format($expenses,2).'.','action'=>'Keep recording all purchases and sales to maintain a useful comparison.','link'=>'reports'];

    $low=[]; foreach($inventory as $r){$q=clean_decimal($r['quantity']??0);$min=clean_decimal($r['minimum_stock']??0);if($min>0&&$q<=$min)$low[]=$r;}
    if($low){usort($low,fn($a,$b)=>clean_decimal($a['quantity']??0)<=>clean_decimal($b['quantity']??0));$names=[];foreach(array_slice($low,0,3) as $r)$names[]=((string)($r['item_name']??'Item')).' ('.(string)($r['quantity']??0).' '.(string)($r['unit']??'').')';$insights[]=['level'=>'high','icon'=>'📦','title'=>count($low).' inventory item(s) at or below minimum','detail'=>'Priority stock: '.implode(', ',$names).'.','action'=>'Check physical stock and plan replenishment before the next farm activity.','link'=>'inventory'];}

    $harvestSoon=[]; foreach($crops as $r){$d=trim((string)($r['expected_harvest_date']??''));if($d==='')continue;try{$dt=new DateTimeImmutable($d);$days=(int)$today->diff($dt)->format('%r%a');if($days<=14)$harvestSoon[]=['name'=>(string)($r['crop_name']??'Crop'),'days'=>$days,'date'=>$d];}catch(Throwable $e){}}
    if($harvestSoon){usort($harvestSoon,fn($a,$b)=>$a['days']<=>$b['days']);$h=$harvestSoon[0];$when=$h['days']<0?'overdue by '.abs($h['days']).' day(s)':($h['days']===0?'due today':'due in '.$h['days'].' day(s)');$insights[]=['level'=>$h['days']<0?'high':'watch','icon'=>'🌾','title'=>'Harvest planning needs attention','detail'=>$h['name'].' is '.$when.' (expected '.$h['date'].').','action'=>'Check crop maturity and update the harvest record after field inspection.','link'=>'crops'];}
    $vetSoon=[]; foreach($vet as $r){$d=trim((string)($r['next_due_date']??''));if($d==='')continue;try{$dt=new DateTimeImmutable($d);$days=(int)$today->diff($dt)->format('%r%a');if($days<=14)$vetSoon[]=['animal'=>(string)($r['animal_type']??'Animal'),'id'=>(string)($r['animal_id']??''),'medicine'=>(string)($r['medicine']??'Treatment'),'days'=>$days,'date'=>$d];}catch(Throwable $e){}}
    if($vetSoon){usort($vetSoon,fn($a,$b)=>$a['days']<=>$b['days']);$v=$vetSoon[0];$when=$v['days']<0?'overdue by '.abs($v['days']).' day(s)':($v['days']===0?'due today':'due in '.$v['days'].' day(s)');$insights[]=['level'=>$v['days']<0?'high':'watch','icon'=>'🩺','title'=>'Veterinary care reminder','detail'=>$v['medicine'].' for '.$v['animal'].' '.$v['id'].' is '.$when.'.','action'=>'Confirm the animal and schedule the treatment with the responsible person.','link'=>'veterinary'];}
    $health=[];foreach($animals as $a){$status=strtolower(trim((string)($a['health_status']??'')));if($status!==''&&(str_contains($status,'sick')||str_contains($status,'ill')||str_contains($status,'poor')||str_contains($status,'injur')))$health[]=$a;}
    if($health)$insights[]=['level'=>'high','icon'=>'🐄','title'=>count($health).' animal record(s) report a health concern','detail'=>'One or more livestock records contain a health status such as sick, ill, poor or injured.','action'=>'Review the animal records and log veterinary assessment or treatment as appropriate.','link'=>'livestock'];
    $missingHarvest=0;foreach($crops as $c)if(trim((string)($c['expected_harvest_date']??''))==='')$missingHarvest++;
    if($missingHarvest>0)$insights[]=['level'=>'info','icon'=>'🗓️','title'=>'Improve crop planning data','detail'=>$missingHarvest.' crop record(s) have no expected harvest date.','action'=>'Add realistic expected harvest dates to improve reminders and planning.','link'=>'crops'];
    if(!$insights)$insights[]=['level'=>'good','icon'=>'✅','title'=>'No priority risks detected by current checks','detail'=>'The records reviewed did not trigger the advisor’s current rules. This does not replace field inspections or professional advice.','action'=>'Keep records updated so future recommendations remain useful.','link'=>'dashboard'];
    return array_slice($insights,0,8);
}


/** Return database-derived, explainable forecasts; no external API or schema changes. */
function ai_forecast_metrics(PDO $pdo): array {
    $now=new DateTimeImmutable('first day of this month');
    $months=[];
    for($i=5;$i>=0;$i--){$dt=$now->modify('-'.$i.' months');$key=$dt->format('Y-m');$months[$key]=['label'=>$dt->format('M Y'),'sales'=>0.0,'procurement'=>0.0];}
    foreach(get_records($pdo,'sales') as $r){$d=substr((string)($r['sale_date']??''),0,7);if(isset($months[$d]))$months[$d]['sales']+=sale_total($r);}
    foreach(get_records($pdo,'procurement') as $r){$d=substr((string)($r['purchase_date']??''),0,7);if(isset($months[$d]))$months[$d]['procurement']+=procurement_total($r);}
    $last3=array_slice(array_values($months),-3);$hasHistory=0;foreach($months as $m){if($m['sales']>0||$m['procurement']>0)$hasHistory++;}
    $salesForecast=count($last3)===3?array_sum(array_column($last3,'sales'))/3:0;
    $expenseForecast=count($last3)===3?array_sum(array_column($last3,'procurement'))/3:0;
    $stockForecast=[];$today=new DateTimeImmutable('today');
    foreach(inventory_rows($pdo) as $item){$name=(string)($item['item_name']??'');$unit=(string)($item['unit']??'');$current=clean_decimal($item['quantity']??0);if($name==='')continue;
        $stmt=$pdo->prepare("SELECT COALESCE(SUM(quantity),0) FROM stock_ledger WHERE item_key=? AND LOWER(movement_type)='out' AND created_at>=DATE_SUB(CURDATE(),INTERVAL 90 DAY)");
        $stmt->execute([stock_key($name,$unit)]);$used=(float)$stmt->fetchColumn();
        if($used>0){$daily=$used/90;$days=$daily>0?$current/$daily:null;$minimum=clean_decimal($item['minimum_stock']??0);$stockForecast[]=['name'=>$name,'unit'=>$unit,'quantity'=>$current,'daily'=>$daily,'days'=>$days,'minimum'=>$minimum,'used90'=>$used];}
    }
    usort($stockForecast,fn($a,$b)=>($a['days']??999999)<=>($b['days']??999999));
    return ['months'=>$months,'sales_forecast'=>$salesForecast,'expense_forecast'=>$expenseForecast,'history_months'=>$hasHistory,'stock_forecast'=>array_slice($stockForecast,0,6)];
}

/** A no-cost intent-based assistant. Answers only from the logged-in app's own records. */
function ai_chat_answer(PDO $pdo, string $question): string {
    $q=strtolower(trim($question));
    if($q==='')return 'Please enter a question about your farm records.';
    $sales=get_records($pdo,'sales');$purchases=get_records($pdo,'procurement');$inventory=get_records($pdo,'inventory');$crops=get_records($pdo,'crops');$animals=get_records($pdo,'livestock');$vet=get_records($pdo,'veterinary');
    if(preg_match('/\b(help|what can you|examples|questions)\b/',$q))return 'You can ask: “How much are my sales?”, “Summarise procurement expenses”, “Which stock items are low?”, “What harvests are due?”, “How many livestock do I have?”, “Which veterinary visits are overdue?”, or “What is my estimated monthly sales average?”. I answer using saved records and clearly state when information is missing.';
    if(preg_match('/\b(stock|inventory|reorder|restock|low stock|suppl)\b/',$q)){
        $low=[];foreach($inventory as $r){$qty=clean_decimal($r['quantity']??0);$min=clean_decimal($r['minimum_stock']??0);if($min>0&&$qty<=$min)$low[]=(string)($r['item_name']??'Item').' ('.number_format($qty,2).' '.(string)($r['unit']??'').'; minimum '.number_format($min,2).')';}
        return $low?'There are '.count($low).' item(s) at or below their configured minimum: '.implode('; ',array_slice($low,0,12)).'. Check physical stock before ordering.':'No items currently meet the configured low-stock rule. This depends on minimum-stock levels being entered correctly.';
    }
    if(preg_match('/\b(vet|veterinary|vaccin|treatment|animal health|overdue care)\b/',$q)){
        $today=new DateTimeImmutable('today');$due=[];foreach($vet as $r){$d=trim((string)($r['next_due_date']??''));if(!$d)continue;try{$dt=new DateTimeImmutable($d);$days=(int)$today->diff($dt)->format('%r%a');if($days<=14)$due[]=(string)($r['medicine']??'Care').' for '.(string)($r['animal_type']??'animal').' '.(string)($r['animal_id']??'').' — '.($days<0?'overdue '.abs($days).' day(s)':($days===0?'due today':'due in '.$days.' day(s)'));}catch(Throwable $e){}}
        return $due?'Veterinary follow-ups due or approaching: '.implode('; ',array_slice($due,0,10)).'. Confirm schedules with the responsible veterinary professional.':'No veterinary records with due dates in the next 14 days or overdue were found.';
    }
    if(preg_match('/\b(harvest|crop|planting|yield)\b/',$q)){
        $today=new DateTimeImmutable('today');$items=[];foreach($crops as $r){$d=trim((string)($r['expected_harvest_date']??''));if(!$d)continue;try{$dt=new DateTimeImmutable($d);$days=(int)$today->diff($dt)->format('%r%a');if($days<=30)$items[]=(string)($r['crop_name']??'Crop').' ('.($days<0?'overdue '.abs($days).' day(s)':($days===0?'due today':'due in '.$days.' day(s)')).'; '.$d.')';}catch(Throwable $e){}}
        return $items?'Harvests due or approaching in 30 days: '.implode('; ',array_slice($items,0,12)).'. Confirm crop maturity in the field before harvesting.':'No crop records with expected harvest dates in the next 30 days or overdue were found. Add expected dates to improve planning.';
    }
    if(preg_match('/\b(livestock|animals|animal count|how many animals|herd)\b/',$q)){
        $types=[];foreach($animals as $a){$type=trim((string)($a['animal_type']??'Unknown'));$types[$type]=($types[$type]??0)+1;}if(!$types)return 'There are no livestock records to count yet.';arsort($types);$parts=[];foreach($types as $type=>$n)$parts[]=$type.': '.$n;return 'There are '.count($animals).' livestock record(s). Breakdown by recorded animal type: '.implode('; ',$parts).'. This counts records, not necessarily verified live animals.';
    }
    if(preg_match('/\b(compare|difference|profit|cash flow|financial|finance|sales and expenses|revenue and expenses)\b/',$q)){
        $revenue=0;$cost=0;foreach($sales as $r)$revenue+=sale_total($r);foreach($purchases as $r)$cost+=procurement_total($r);return 'Recorded sales revenue: MWK '.number_format($revenue,2).'. Recorded procurement: MWK '.number_format($cost,2).'. Difference: MWK '.number_format($revenue-$cost,2).'. This is sales minus procurement only, not a complete accounting profit or cash-flow statement.';
    }
    if(preg_match('/\b(procurement|purchase|purchases|expense|expenses|spent|spending|cost)\b/',$q)&&!preg_match('/\b(sale|sales|revenue|income)\b/',$q)){
        $total=0;$count=0;foreach($purchases as $r){$total+=procurement_total($r);$count++;}return 'Recorded procurement totals MWK '.number_format($total,2).' across '.$count.' purchase record(s). This includes procurement records only, not all possible operating costs.';
    }
    if(preg_match('/\b(sale|sales|revenue|income|earned)\b/',$q)){
        $total=0;$count=0;foreach($sales as $r){$total+=sale_total($r);$count++;}return 'Recorded sales revenue is MWK '.number_format($total,2).' across '.$count.' sales record(s). Revenue is calculated from quantity × selling price; it is not the same as profit.';
    }
    if(preg_match('/\b(profit|cash flow|net|margin|financial|finance)\b/',$q)){
        $revenue=0;$cost=0;foreach($sales as $r)$revenue+=sale_total($r);foreach($purchases as $r)$cost+=procurement_total($r);return 'Recorded sales revenue: MWK '.number_format($revenue,2).'. Recorded procurement: MWK '.number_format($cost,2).'. Difference: MWK '.number_format($revenue-$cost,2).'. This is sales minus procurement only, not a complete accounting profit or cash-flow statement.';
    }
    return 'I could not confidently match that question to a supported analysis. Try asking about sales revenue, procurement expenses, low stock, upcoming harvests, livestock counts, veterinary follow-ups, or financial comparisons. Type “help” to see examples.';
}

function app_is_admin(): bool {
    return in_array(strtolower((string)($_SESSION['role'] ?? '')), ['admin','administrator','super admin','superadmin'], true);
}
function make_uuid(): string {
    return sprintf('%s-%s-%s-%s-%s', bin2hex(random_bytes(4)), bin2hex(random_bytes(2)), bin2hex(random_bytes(2)), bin2hex(random_bytes(2)), bin2hex(random_bytes(6)));
}
function backup_payload(PDO $pdo): array {
    $records=$pdo->query("SELECT record_uuid,module,title,data_json,created_by,updated_by,created_at,updated_at FROM records ORDER BY id ASC")->fetchAll();
    $stock=$pdo->query("SELECT item_key,item_name,movement_type,quantity,reference_module,reference_uuid,notes,user_id,created_at FROM stock_ledger ORDER BY id ASC")->fetchAll();
    $settings=$pdo->query("SELECT setting_key,setting_value,updated_by,updated_at FROM settings ORDER BY setting_key ASC")->fetchAll();
    foreach($records as &$r){$decoded=json_decode((string)$r['data_json'],true);$r['data']=is_array($decoded)?$decoded:[];unset($r['data_json']);}
    unset($r);
    return [
        'format'=>'FWU-BACKUP',
        'version'=>'2.0',
        'exportedAt'=>date('c'),
        'application'=>'Farming with Urunji Management System',
        'source'=>'server-mysql',
        'data'=>[
            'records'=>$records,
            'stock_ledger'=>$stock,
            'settings'=>$settings
        ]
    ];
}
function legacy_backup_records(array $data): array {
    $map=[
      'mufms_crops'=>'crops','mufms_harvests'=>'harvests','mufms_livestock'=>'livestock','mufms_veterinary'=>'veterinary',
      'mufms_inventory'=>'inventory','mufms_procurements'=>'procurement','mufms_sales'=>'sales','mufms_customers'=>'customers',
      'mufms_employees'=>'employees','mufms_suppliers'=>'suppliers','mufms_fields'=>'fields'
    ];
    $out=[];
    foreach($map as $key=>$module){
        $rows=$data[$key]??[]; if(!is_array($rows)) continue;
        foreach($rows as $src){ if(!is_array($src)) continue; $d=[];
            if($module==='crops') $d=['crop_name'=>$src['name']??$src['crop']??'','variety'=>$src['variety']??'','field_name'=>$src['field']??'','planting_date'=>$src['plant']??$src['plantingDate']??'','expected_harvest_date'=>$src['harvest']??$src['expectedHarvest']??'','area'=>$src['area']??'','unit'=>$src['unit']??'','quantity'=>$src['quantity']??'','status'=>$src['status']??'','notes'=>$src['notes']??''];
            elseif($module==='harvests') $d=['crop_name'=>$src['crop']??$src['product']??'','harvest_date'=>$src['date']??$src['harvestDate']??'','quantity'=>$src['quantity']??'','unit'=>$src['unit']??'','field_name'=>$src['field']??'','quality'=>$src['quality']??'','notes'=>$src['notes']??''];
            elseif($module==='livestock') $d=['animal_id'=>$src['id']??$src['tagNumber']??'','animal_type'=>$src['type']??$src['species']??'','breed'=>$src['breed']??'','gender'=>$src['gender']??'','birth_date'=>$src['birthDate']??'','weight'=>$src['weight']??0,'location'=>$src['location']??'','purchase_cost'=>$src['purchaseCost']??0,'current_value'=>$src['currentValue']??0,'health_status'=>$src['health']??$src['healthStatus']??'','vaccination'=>$src['vaccination']??'','supplier'=>$src['supplier']??'','notes'=>$src['notes']??''];
            elseif($module==='veterinary') $d=['animal_id'=>$src['animalId']??'','animal_type'=>$src['animalType']??'','treatment_date'=>$src['dateGiven']??$src['date']??'','medicine'=>$src['medicine']??'','dosage'=>$src['dosage']??'','next_due_date'=>$src['nextDue']??'','cost'=>$src['cost']??0,'notes'=>trim(($src['purpose']??'').' '.($src['notes']??''))];
            elseif($module==='inventory') $d=['item_name'=>$src['name']??$src['productName']??$src['item']??'','category'=>$src['category']??'','quantity'=>$src['quantity']??$src['stock']??0,'unit'=>$src['unit']??'units','unit_price'=>$src['price']??$src['unitPrice']??0,'supplier_name'=>$src['supplier']??'','minimum_stock'=>$src['minimum']??$src['minStock']??0,'notes'=>$src['notes']??''];
            elseif($module==='procurement') $d=['item_name'=>$src['item']??$src['product']??$src['productName']??$src['name']??'','quantity'=>$src['quantity']??$src['qty']??0,'unit'=>$src['unit']??'units','unit_cost'=>$src['unitCost']??$src['unit_cost']??$src['cost']??$src['purchasePrice']??0,'supplier'=>$src['supplier']??$src['supplierName']??'','purchase_date'=>$src['date']??$src['purchaseDate']??'','reference'=>$src['reference']??'','notes'=>$src['notes']??''];
            elseif($module==='sales') $d=['item_name'=>$src['product']??$src['item']??$src['productName']??$src['name']??'','quantity'=>$src['quantity']??$src['qty']??0,'unit'=>$src['unit']??'units','unit_price'=>$src['unitPrice']??$src['unit_price']??$src['price']??$src['sellingPrice']??0,'customer'=>$src['customer']??$src['customerName']??'','sale_date'=>$src['date']??$src['saleDate']??'','reference'=>$src['reference']??'','notes'=>$src['notes']??''];
            elseif($module==='customers') $d=['name'=>$src['name']??'','phone'=>$src['phone']??'','email'=>$src['email']??'','address'=>$src['address']??'','notes'=>$src['notes']??''];
            elseif($module==='employees') $d=['employee_name'=>$src['name']??$src['employee_name']??'','job_title'=>$src['role']??$src['job_title']??'','phone'=>$src['phone']??'','salary'=>$src['salary']??0,'status'=>$src['status']??'','notes'=>trim(($src['department']??'').' '.($src['notes']??''))];
            elseif($module==='suppliers') $d=['name'=>$src['name']??'','phone'=>$src['phone']??'','email'=>$src['email']??'','address'=>$src['address']??'','notes'=>$src['products']??$src['notes']??''];
            elseif($module==='fields') $d=['field_name'=>$src['name']??$src['field_name']??'','area'=>$src['area']??0,'unit'=>$src['unit']??'','crop'=>$src['crop']??'','location'=>$src['location']??$src['gps']??'','status'=>$src['status']??'','notes'=>$src['notes']??''];
            if($module==='finance') continue;
            if($d) $out[]=['record_uuid'=>make_uuid(),'module'=>$module,'title'=>record_title($module,$d),'data'=>$d,'created_by'=>null,'updated_by'=>null,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')];
        }
    }
    $finance=$data['mufms_finance_transactions']??$data['mufms_transactions']??[];
    if(is_array($finance)) foreach($finance as $src) if(is_array($src)) { $d=['transaction_type'=>$src['type']??$src['transaction_type']??'Expense','description'=>$src['description']??'','amount'=>$src['amount']??0,'transaction_date'=>$src['date']??'','category'=>$src['category']??'','reference'=>$src['reference']??'','notes'=>$src['notes']??'']; $out[]=['record_uuid'=>make_uuid(),'module'=>'finance','title'=>record_title('finance',$d),'data'=>$d,'created_by'=>null,'updated_by'=>null,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]; }
    return $out;
}
function normalise_import_backup(array $backup): array {
    // Module definitions live in global scope; expose them here so valid records are not all filtered out.
    global $MODULES;
    // Accept the current FWU format and older/plain JSON exports containing mufms_* arrays.
    if(($backup['format']??'')==='FWU-BACKUP' && isset($backup['data']) && is_array($backup['data'])) {
        $data=$backup['data'];
    } elseif(isset($backup['data']) && is_array($backup['data'])) {
        $data=$backup['data'];
    } else {
        $known=array_filter(array_keys($backup),fn($k)=>preg_match('/^(mufms_|stock_|stock|settings)/i',(string)$k));
        if(!$known) throw new RuntimeException('Invalid FWU JSON backup. The file does not contain recognised FWU data.');
        $data=$backup;
    }
    $records=[]; $stock=[]; $settings=[];

    if(isset($data['records']) && is_array($data['records'])) {
        foreach($data['records'] as $r){
            if(!is_array($r) || empty($r['module']) || !isset($r['data']) || !is_array($r['data'])) continue;
            $module=(string)$r['module'];
            if(!isset($MODULES[$module])) continue;
            if(!isset($r['record_uuid'])||!preg_match('/^[a-zA-Z0-9-]{8,80}$/',(string)$r['record_uuid'])) $r['record_uuid']=make_uuid();
            $r['title']=record_title($module,$r['data']);
            if ($module === 'advisory') $r['data']['venture'] = 'shop'; elseif (isset($r['data']['venture'])) $r['data']['venture'] = canonical_venture($r['data']['venture'], $module);
            // Recalculate sale totals on import so an old/incorrect stored total can never affect reporting.
            if($module==='sales') $r['data']['total_price']=sale_total($r['data']);
            if($module==='procurement') $r['data']['total_cost']=procurement_total($r['data']);
            $records[]=$r;
        }
        $stock=is_array($data['stock_ledger']??null)?$data['stock_ledger']:[];
        $settings=is_array($data['settings']??null)?$data['settings']:[];
    } else {
        $records=legacy_backup_records($data);
        if(isset($data['mufms_farm']) && is_array($data['mufms_farm'])) {
            $farm=$data['mufms_farm'];
            $settings=[['setting_key'=>'farm_name','setting_value'=>$farm['name']??$farm['farmName']??'Farming with Urunji Farm','updated_by'=>null,'updated_at'=>date('Y-m-d H:i:s')]];
        }
        // Older backups may store ledger data under several names.
        foreach(['stock_ledger','stockLedger','mufms_stock_ledger','mufms_stockLedger','stock_movements','stockMovements'] as $key){
            if(isset($data[$key]) && is_array($data[$key])) { $stock=$data[$key]; break; }
        }
    }

    // Accept both current and common legacy field names for stock movements.
    $normalStock=[];
    foreach($stock as $m){
        if(!is_array($m)) continue;
        $item=(string)($m['item_name']??$m['itemName']??$m['item']??$m['product']??$m['name']??'');
        $unit=(string)($m['unit']??'units');
        $qty=clean_decimal($m['quantity']??$m['qty']??$m['amount']??0);
        $movement=(string)($m['movement_type']??$m['movementType']??$m['type']??$m['movement']??'');
        $movement=strtoupper(trim($movement));
        if($movement==='INCREASE'||$movement==='PURCHASE'||$movement==='RECEIPT'||$movement==='RECEIVED')$movement='IN';
        if($movement==='DECREASE'||$movement==='SALE'||$movement==='ISSUE'||$movement==='SOLD')$movement='OUT';
        if($movement!=='IN' && $movement!=='OUT') continue;
        if($item==='' || $qty<=0) continue;
        $legacyKey=(string)($m['item_key']??$m['itemKey']??''); $keyVenture=str_contains($legacyKey,'|')?explode('|',$legacyKey,2)[0]:'shop';
        $movementVenture=canonical_venture($m['venture']??$keyVenture,(string)($m['reference_module']??$m['module']??''));
        $normalStock[]=[
            'item_key'=>stock_key($item,$unit,$movementVenture),
            'item_name'=>$item,
            'movement_type'=>$movement,
            'quantity'=>$qty,
            'reference_module'=>(string)($m['reference_module']??$m['referenceModule']??$m['module']??''),
            'reference_uuid'=>(string)($m['reference_uuid']??$m['referenceUuid']??$m['uuid']??''),
            'notes'=>(string)($m['notes']??$m['note']??''),
            'user_id'=>null,
            'created_at'=>(string)($m['created_at']??$m['createdAt']??$m['date']??date('Y-m-d H:i:s'))
        ];
    }

    // If an old backup did not export the ledger, rebuild it from Procurement and Sales records.
    if(!$normalStock){
        foreach($records as $r){
            $module=(string)($r['module']??''); $d=$r['data']??[];
            if($module==='procurement'){
                $item=(string)($d['item_name']??''); $unit=(string)($d['unit']??'units'); $qty=clean_decimal($d['quantity']??0); $venture=canonical_venture($d['venture']??'shop','procurement');
                if($item!==''&&$qty>0)$normalStock[]=['item_key'=>stock_key($item,$unit,$venture),'item_name'=>$item,'movement_type'=>'IN','quantity'=>$qty,'reference_module'=>'procurement','reference_uuid'=>(string)($r['record_uuid']??''),'notes'=>'Rebuilt from imported procurement','user_id'=>null,'created_at'=>(string)($d['purchase_date']??date('Y-m-d H:i:s'))];
            } elseif($module==='sales'){
                $item=(string)($d['item_name']??''); $unit=(string)($d['unit']??'units'); $qty=clean_decimal($d['quantity']??0); $venture=canonical_venture($d['venture']??'shop','sales');
                if($item!==''&&$qty>0)$normalStock[]=['item_key'=>stock_key($item,$unit,$venture),'item_name'=>$item,'movement_type'=>'OUT','quantity'=>$qty,'reference_module'=>'sales','reference_uuid'=>(string)($r['record_uuid']??''),'notes'=>'Rebuilt from imported sale','user_id'=>null,'created_at'=>(string)($d['sale_date']??date('Y-m-d H:i:s'))];
            }
        }
    }
    return ['records'=>$records,'stock'=>$normalStock,'settings'=>$settings,'legacy'=>!isset($data['records'])];
}

start_app_session();
$flash=get_flash();

if(isset($_GET['logout'])){
    $_SESSION=[]; session_destroy(); redirect('index.php?page=login');
}

if($_SERVER['REQUEST_METHOD']==='POST'){
    try{
        $action=$_POST['action']??''; 
        if($action==='login'){
            $u=trim((string)($_POST['username']??''));$p=(string)($_POST['password']??'');
            if($u===''||$p==='')throw new RuntimeException('Username and password are required.');
            $pdo=db();$s=$pdo->prepare("SELECT * FROM users WHERE username=? AND active=1 LIMIT 1");$s->execute([$u]);$user=$s->fetch();
            if(!$user||!password_verify($p,$user['password_hash']))throw new RuntimeException('Invalid username or password.');
            session_regenerate_id(false);$_SESSION['user_id']=(int)$user['id'];$_SESSION['username']=$user['username'];$_SESSION['role']=$user['role'];$_SESSION['login_at']=date('c');
            $pdo->prepare("UPDATE users SET last_login_at=NOW() WHERE id=?")->execute([(int)$user['id']]); redirect('index.php');
        }
        require_login(); verify_csrf(); $pdo=db(); $uid=(int)$_SESSION['user_id'];
        if($action==='ask_ai'){
            $question=trim((string)($_POST['question']??''));
            if(strlen($question)>500)throw new RuntimeException('Please keep questions under 500 characters.');
            $_SESSION['ai_last_question']=$question; $_SESSION['ai_last_answer']=ai_chat_answer($pdo,$question);
            redirect('index.php?page=ai_assistant');
        }
        if($action==='reset_system'){
            if(!app_is_admin()) throw new RuntimeException('Only an administrator can reset the farm data.');
            $confirm=trim((string)($_POST['confirm_reset']??''));
            if($confirm!=='RESET') throw new RuntimeException('Type RESET to confirm the system data reset.');
            $pdo->beginTransaction();
            $pdo->exec('DELETE FROM stock_ledger');
            $pdo->exec('DELETE FROM records');
            $pdo->exec('DELETE FROM settings');
            audit($pdo,$uid,'RESET','system',null,['scope'=>'records,stock_ledger,settings','preserved'=>'users']);
            $pdo->commit();
            flash('success','System data reset successfully. User accounts were preserved. You can now upload the new JSON backup.');
            redirect('index.php?page=settings');
        }
        if($action==='save_record'){
            $module=(string)($_POST['module']??''); if(!isset($MODULES[$module]))throw new RuntimeException('Unknown module.');
            $uuid=trim((string)($_POST['uuid']??'')); $data=[];
            foreach($MODULES[$module]['fields'] as $key=>$meta){$v=trim((string)($_POST[$key]??'')); if(($meta[1]??'')==='number'&&$v!=='')$v=(float)$v; $data[$key]=$v;}
            if ($module === 'advisory') $data['venture'] = 'shop';
            else $data['venture'] = canonical_venture($data['venture'] ?? '', $module);
            if($module==='employees' && !empty($data['date_joined'])) { try { $joined=new DateTimeImmutable((string)$data['date_joined']); $data['years_worked']=max(0,(int)$joined->diff(new DateTimeImmutable('today'))->y); } catch(Throwable $e) {} }
            if($module==='sales') $data['total_price']=sale_total($data);
            if (in_array($module, ['sales','procurement'], true)) {
                if (clean_decimal($data['quantity'] ?? 0) <= 0) throw new RuntimeException('Quantity must be greater than zero.');
                $priceField = $module === 'sales' ? 'unit_price' : 'unit_cost';
                if (clean_decimal($data[$priceField] ?? 0) < 0) throw new RuntimeException('Price or cost cannot be negative.');
                if (trim((string)($data['item_name'] ?? '')) === '' || trim((string)($data['unit'] ?? '')) === '') throw new RuntimeException('Item name and unit are required.');
            }
            if ($module === 'inventory' && clean_decimal($data['quantity'] ?? 0) < 0) throw new RuntimeException('Inventory quantity cannot be negative. Use a stock adjustment or sale to reduce stock.');
            if ($module === 'inventory' && !$uuid && clean_decimal($data['quantity'] ?? 0) <= 0) throw new RuntimeException('Enter a quantity greater than zero for a new stock entry.');
            if ($module === 'finance' && clean_decimal($data['amount'] ?? 0) < 0) throw new RuntimeException('Finance amount cannot be negative. Choose the correct transaction type instead.');
            foreach($MODULES[$module]['fields'] as $key=>$meta){if(in_array('required',$meta,true)&&trim((string)($data[$key]??''))==='')throw new RuntimeException($meta[0].' is required.');}
            $pdo->beginTransaction();
            $old=$uuid?find_record($pdo,$uuid):null;
            if ($old && (string)($old['row']['module'] ?? $module) !== $module) throw new RuntimeException('This record does not belong to the selected module.');
            // New inventory entry for an existing product adds to stock instead of creating a duplicate.
            // Match product names case-insensitively, but keep venture and unit separate.
            if($module==='inventory' && !$old){
                $incomingName=trim((string)($data['item_name']??''));
                $incomingUnit=trim((string)($data['unit']??'units'));
                $incomingVenture=(string)($data['venture']??'shop');
                foreach(inventory_rows($pdo) as $existingItem){
                    if(normalise_item_name((string)($existingItem['item_name']??''))===normalise_item_name($incomingName)
                       && normalise_item_name((string)($existingItem['unit']??'units'))===normalise_item_name($incomingUnit)
                       && canonical_venture($existingItem['venture']??'shop')===canonical_venture($incomingVenture)){
                        $merged=$existingItem;
                        $merged['quantity']=clean_decimal($existingItem['quantity']??0)+clean_decimal($data['quantity']??0);
                        foreach(['category','unit_price','supplier_name','minimum_stock','notes'] as $field){ if(($data[$field]??'')!=='' && ($data[$field]??null)!==null) $merged[$field]=$data[$field]; }
                        $newUuid=save_record_internal($pdo,'inventory',$merged,(string)$existingItem['_uuid'],$uid);
                        audit($pdo,$uid,'STOCK_ADD','inventory',$newUuid,['item'=>$incomingName,'added'=>clean_decimal($data['quantity']??0),'new_total'=>$merged['quantity'],'venture'=>$incomingVenture]);
                        $ledger=$pdo->prepare("INSERT INTO stock_ledger(item_key,item_name,movement_type,quantity,reference_module,reference_uuid,notes,user_id) VALUES(?,?,?,?,?,?,?,?)");
                        $ledger->execute([stock_key($incomingName,$incomingUnit,$incomingVenture),$incomingName,'IN',clean_decimal($data['quantity']??0),'inventory',$newUuid,'Manual stock addition / matched existing item',$uid]);
                        $pdo->commit(); flash('success','Existing item matched regardless of capital letters. Added '.number_format(clean_decimal($data['quantity']??0),3).' '.$incomingUnit.'; new stock is '.number_format((float)$merged['quantity'],3).' '.$incomingUnit.'.'); redirect('index.php?page=inventory');
                    }
                }
            }
            if($module==='procurement'){
                if($old){$od=$old['data'];adjust_stock($pdo,(string)($od['item_name']??''),-clean_decimal($od['quantity']??0),(string)($od['unit']??'units'),$module,$uuid,$uid,'Reverse old procurement before edit',(string)($od['venture']??'shop'));}
                $newUuid=save_record_internal($pdo,$module,$data,$uuid?:null,$uid);
                adjust_stock($pdo,(string)$data['item_name'],clean_decimal($data['quantity']), (string)$data['unit'],$module,$newUuid,$uid,'Procurement received',(string)($data['venture']??'shop'));
                audit($pdo,$uid,$old?'UPDATE':'CREATE',$module,$newUuid,$data);
            } elseif($module==='sales'){
                if($old){$od=$old['data'];adjust_stock($pdo,(string)($od['item_name']??''),clean_decimal($od['quantity']??0),(string)($od['unit']??'units'),$module,$uuid,$uid,'Return old sale quantity before edit',(string)($od['venture']??'shop'));}
                $newUuid=save_record_internal($pdo,$module,$data,$uuid?:null,$uid);
                adjust_stock($pdo,(string)$data['item_name'],-clean_decimal($data['quantity']),(string)$data['unit'],$module,$newUuid,$uid,'Sale issued from stock',(string)($data['venture']??'shop'));
                audit($pdo,$uid,$old?'UPDATE':'CREATE',$module,$newUuid,$data);
            } else {
                $newUuid=save_record_internal($pdo,$module,$data,$uuid?:null,$uid);
                if($module==='inventory'){
                    $ledger=$pdo->prepare("INSERT INTO stock_ledger(item_key,item_name,movement_type,quantity,reference_module,reference_uuid,notes,user_id) VALUES(?,?,?,?,?,?,?,?)");
                    if(!$old && clean_decimal($data['quantity']??0)>0){
                        $ledger->execute([stock_key((string)$data['item_name'],(string)$data['unit'],(string)($data['venture']??'shop')),(string)$data['item_name'],'IN',clean_decimal($data['quantity']),'inventory',$newUuid,'Initial manual stock entry',$uid]);
                    } elseif($old){
                        $od=$old['data'];
                        $oldName=(string)($od['item_name']??'');$oldUnit=(string)($od['unit']??'units');$oldVenture=canonical_venture($od['venture']??'shop','inventory');
                        $newName=(string)($data['item_name']??'');$newUnit=(string)($data['unit']??'units');$newVenture=canonical_venture($data['venture']??'shop','inventory');
                        $oldQty=clean_decimal($od['quantity']??0);$newQty=clean_decimal($data['quantity']??0);
                        $sameKey=stock_key($oldName,$oldUnit,$oldVenture)===stock_key($newName,$newUnit,$newVenture);
                        if(!$sameKey && $oldQty>0)$ledger->execute([stock_key($oldName,$oldUnit,$oldVenture),$oldName,'OUT',$oldQty,'inventory',$newUuid,'Inventory item identity changed; previous balance removed',$uid]);
                        if(!$sameKey && $newQty>0)$ledger->execute([stock_key($newName,$newUnit,$newVenture),$newName,'IN',$newQty,'inventory',$newUuid,'Inventory item identity changed; new balance created',$uid]);
                        if($sameKey && abs($newQty-$oldQty)>0.000001){$delta=$newQty-$oldQty;$ledger->execute([stock_key($newName,$newUnit,$newVenture),$newName,$delta>0?'IN':'OUT',abs($delta),'inventory',$newUuid,'Manual inventory quantity adjustment',$uid]);}
                    }
                }
                if($module==='employees' && clean_decimal($data['salary_payment_amount']??0)>0 && !empty($data['salary_payment_date'])) {
                    $salaryRef='Salary payment: '.$data['employee_name'].' | '.$data['salary_payment_date'];
                    $already=false;
                    foreach(get_records($pdo,'finance') as $fr) { if(($fr['reference']??'')===$salaryRef) {$already=true;break;} }
                    if(!$already) {
                        $fd=['venture'=>$data['venture']??'shop','transaction_type'=>'expense','description'=>'Salary payment - '.$data['employee_name'],'amount'=>clean_decimal($data['salary_payment_amount']),'transaction_date'=>$data['salary_payment_date'],'category'=>'Salaries & Wages','reference'=>$salaryRef,'notes'=>'Automatically generated from employee salary payment record.'];
                        save_record_internal($pdo,'finance',$fd,null,$uid);
                        audit($pdo,$uid,'CREATE','finance',null,$fd);
                    }
                }
                audit($pdo,$uid,$old?'UPDATE':'CREATE',$module,$newUuid,$data);
            }
            $pdo->commit();flash('success',$old?'Record updated in the central database.':'Record saved to the central database.');redirect('index.php?page='.$module);
        }
        if($action==='delete_record'){
            $module=(string)($_POST['module']??'');$uuid=(string)($_POST['uuid']??'');if(!isset($MODULES[$module]))throw new RuntimeException('Unknown module.');$old=find_record($pdo,$uuid);if(!$old)throw new RuntimeException('Record not found.');if((string)($old['row']['module']??$module)!==$module)throw new RuntimeException('This record does not belong to the selected module.');
            $pdo->beginTransaction();
            if($module==='procurement'){ $d=$old['data'];adjust_stock($pdo,(string)$d['item_name'],-clean_decimal($d['quantity']),(string)$d['unit'],$module,$uuid,$uid,'Reverse deleted procurement',(string)($d['venture']??'shop')); }
            if($module==='sales'){ $d=$old['data'];adjust_stock($pdo,(string)$d['item_name'],clean_decimal($d['quantity']),(string)$d['unit'],$module,$uuid,$uid,'Return deleted sale to stock',(string)($d['venture']??'shop')); }
            $pdo->prepare("DELETE FROM records WHERE record_uuid=?")->execute([$uuid]);audit($pdo,$uid,'DELETE',$module,$uuid,$old['data']);$pdo->commit();
            flash('success','Record deleted from the central database.');redirect('index.php?page='.$module);
        }
        if($action==='export_backup'){
            $payload=backup_payload($pdo);
            $filename='FWU-Backup-'.date('Y-m-d_H-i-s').'.json';
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            echo json_encode($payload,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit;
        }
        if($action==='import_backup'){
            if(!app_is_admin()) throw new RuntimeException('Only an administrator can restore a backup.');
            if(empty($_FILES['backup_file']) || ($_FILES['backup_file']['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK) throw new RuntimeException('Please select a valid JSON backup file.');
            if((int)($_FILES['backup_file']['size']??0)>25*1024*1024) throw new RuntimeException('Backup file is too large. Maximum size is 25 MB.');
            $raw=file_get_contents($_FILES['backup_file']['tmp_name']); if($raw===false) throw new RuntimeException('The backup file could not be read.');
            $backup=json_decode($raw,true); if(!is_array($backup)) throw new RuntimeException('The backup file contains invalid JSON.');
            $normal=normalise_import_backup($backup);
            if(!$normal['records']) throw new RuntimeException('The backup contains no restorable records.');
            $confirm=(string)($_POST['confirm_restore']??''); if($confirm!=='RESTORE') throw new RuntimeException('Type RESTORE to confirm replacement of the current records.');
            $pdo->beginTransaction();
            $pdo->exec('DELETE FROM stock_ledger');
            $pdo->exec('DELETE FROM records');
            $pdo->exec('DELETE FROM settings');
            $ins=$pdo->prepare('INSERT INTO records(record_uuid,module,title,data_json,created_by,updated_by,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?)');
            foreach($normal['records'] as $r){
                $created=(string)($r['created_at']??date('Y-m-d H:i:s')); $updated=(string)($r['updated_at']??$created);
                $ins->execute([(string)$r['record_uuid'],(string)$r['module'],(string)($r['title']??record_title((string)$r['module'],$r['data'])),json_encode($r['data'],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),null,$uid,$created,$updated]);
            }
            if($normal['stock']){
                $si=$pdo->prepare('INSERT INTO stock_ledger(item_key,item_name,movement_type,quantity,reference_module,reference_uuid,notes,user_id,created_at) VALUES(?,?,?,?,?,?,?,?,?)');
                foreach($normal['stock'] as $r){ if(!is_array($r)) continue; $si->execute([(string)($r['item_key']??''),(string)($r['item_name']??''),(string)($r['movement_type']??'IN'),(float)($r['quantity']??0),$r['reference_module']??null,$r['reference_uuid']??null,$r['notes']??null,$r['user_id']??null,(string)($r['created_at']??date('Y-m-d H:i:s'))]); }
            }
            $ss=$pdo->prepare('INSERT INTO settings(setting_key,setting_value,updated_by,updated_at) VALUES(?,?,?,?)');
            foreach($normal['settings'] as $r){ if(!is_array($r)||empty($r['setting_key'])) continue; $ss->execute([(string)$r['setting_key'],(string)($r['setting_value']??''),$uid,(string)($r['updated_at']??date('Y-m-d H:i:s'))]); }
            audit($pdo,$uid,'RESTORE','system',null,['records'=>count($normal['records']),'legacy_format'=>$normal['legacy'],'filename'=>$_FILES['backup_file']['name']??'backup.json']);
            $pdo->commit();
            flash('success','Backup restored successfully. '.number_format(count($normal['records'])).' records are now in the central database.'); redirect('index.php?page=dashboard');
        }
        if($action==='add_user'){
            if(!app_is_admin()) throw new RuntimeException('Only an administrator can create users.');
            $username=trim((string)($_POST['new_username']??'')); $password=(string)($_POST['new_password']??''); $role=trim((string)($_POST['new_role']??'worker'));
            if($username==='' || $password==='') throw new RuntimeException('Username and temporary password are required.');
            if(strlen($password)<8) throw new RuntimeException('Temporary password must be at least 8 characters.');
            if(!in_array($role,['worker','manager','accountant','admin'],true)) $role='worker';
            $s=$pdo->prepare('INSERT INTO users(username,password_hash,role,active) VALUES(?,?,?,1)'); $s->execute([$username,password_hash($password,PASSWORD_DEFAULT),$role]);
            audit($pdo,$uid,'CREATE','users',null,['username'=>$username,'role'=>$role]); flash('success','User created successfully.'); redirect('index.php?page=settings');
        }
        if($action==='toggle_user'){
            if(!app_is_admin()) throw new RuntimeException('Only an administrator can enable or disable users.');
            $userId=(int)($_POST['user_id']??0); if($userId<=0 || $userId===$uid) throw new RuntimeException('The current administrator cannot disable their own account.');
            $s=$pdo->prepare('UPDATE users SET active=IF(active=1,0,1) WHERE id=?'); $s->execute([$userId]); if(!$s->rowCount()) throw new RuntimeException('User not found.');
            audit($pdo,$uid,'TOGGLE','users',null,['user_id'=>$userId]); flash('success','User status updated.'); redirect('index.php?page=settings');
        }
        if($action==='save_setting'){
            $key=trim((string)$_POST['key']);$val=trim((string)$_POST['value']);$s=$pdo->prepare("INSERT INTO settings(setting_key,setting_value,updated_by) VALUES(?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),updated_by=VALUES(updated_by),updated_at=CURRENT_TIMESTAMP");$s->execute([$key,$val,$uid]);flash('success','Settings saved.');redirect('index.php?page=settings');
        }
        if($action==='save_theme'){
            $theme=(string)($_POST['theme']??'light'); if(!in_array($theme,['light','dark'],true)) $theme='light';
            $s=$pdo->prepare("INSERT INTO settings(setting_key,setting_value,updated_by) VALUES('theme',?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),updated_by=VALUES(updated_by),updated_at=CURRENT_TIMESTAMP");$s->execute([$theme,$uid]);
            redirect('index.php?page='.urlencode((string)($_POST['return_page']??'dashboard')));
        }
        throw new RuntimeException('Unknown action.');
    }catch(Throwable $ex){
        if(isset($pdo)&&$pdo instanceof PDO&&$pdo->inTransaction())$pdo->rollBack();
        flash('error',$ex->getMessage());redirect('index.php'.(!empty($_GET['page'])?'?page='.urlencode($_GET['page']):''));
    }
}

$page=$_GET['page']??'dashboard';
if(isset($_GET['clear'])&&($_GET['page']??'')==='ai_assistant'){unset($_SESSION['ai_last_question'],$_SESSION['ai_last_answer']);}
if(empty($_SESSION['user_id']))$page='login';
$pdo=null;$records=[];$edit=null;$stats=[];$theme='light';
try{$pdo=db();}catch(Throwable $e){$dbError=$e->getMessage();}
// Stop safely if the database connection failed. Never allow a null PDO to reach page queries.
if (isset($dbError) && !($page === 'login' && empty($_SESSION['user_id']))) {
    http_response_code(503);
    $safeDbError = (string)$dbError;
    $isPlaceholder = str_contains(DB_HOST, 'YOUR_') || str_contains(DB_NAME, 'YOUR_') || str_contains(DB_USER, 'YOUR_');
    ?>
    <!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Farming with Urunji — Database connection</title><link rel="stylesheet" href="assets/style.css"></head>
    <body><main class="main" style="max-width:900px;margin:7vh auto;padding:24px"><div class="panel" style="padding:28px"><div class="eyebrow">FARMING WITH URUNJI</div><h1>Database connection required</h1>
    <p>The application could not connect to MySQL, so it has stopped safely instead of crashing with a PHP TypeError. Your records have not been changed by this page.</p>
    <?php if($isPlaceholder): ?><p><strong>Cause found:</strong> <code>config.php</code> still contains example values such as <code>YOUR_INFINITYFREE_MYSQL_HOST</code>. Replace them with the real settings for your database.</p><?php endif; ?>
    <h3>Check <code>config.php</code></h3><ol><li>For XAMPP, use the MySQL server host normally <code>localhost</code> and the database name you created/imported in phpMyAdmin.</li><li>Use the MySQL username and password configured in your XAMPP installation (often <code>root</code> and an empty password on a default local setup).</li><li>For InfinityFree, use the exact MySQL hostname, database name, username and password shown in the hosting control panel; do not use <code>localhost</code>.</li><li>Confirm MySQL is running and that the database contains the system tables, then refresh this page.</li></ol>
    <details><summary>Technical error details</summary><pre style="white-space:pre-wrap;overflow-wrap:anywhere"><?=htmlspecialchars($safeDbError, ENT_QUOTES, 'UTF-8')?></pre></details>
    <p><a class="btn primary" href="index.php?page=login">Return to login</a></p></div></main></body></html>
    <?php exit;
}
if(!isset($dbError)){ try{$ts=$pdo->query("SELECT setting_value FROM settings WHERE setting_key='theme' LIMIT 1")->fetchColumn(); if(in_array($ts,['light','dark'],true))$theme=$ts;}catch(Throwable $e){} }
$notificationItems=[];
if(!isset($dbError)&&$page!=='login'){
    if(isset($MODULES[$page])){
        $records=get_records($pdo,$page);
        if(isset($_GET['edit'])){$edit=find_record($pdo,(string)$_GET['edit']); if($edit && (string)($edit['row']['module']??$page)!==$page)$edit=null;}
        $ventureFilter = (string)($_GET['venture'] ?? 'all');
        if (in_array($ventureFilter, ['shop','crops','cattle'], true) && $page !== 'employees') {
            $records = array_values(array_filter($records, function($r) use ($ventureFilter, $page) { return canonical_venture($r['venture'] ?? '', $page) === $ventureFilter; }));
        }
    }
    // Build live notification data for the header on every page.
    $nLow=[];$nHarvest=[];$nVet=[];$nToday=new DateTimeImmutable('today');
    foreach(get_records($pdo,'inventory') as $nr){$q=clean_decimal($nr['quantity']??0);$min=clean_decimal($nr['minimum_stock']??0);if($q<=$min&&$min>0)$nLow[]=$nr;}
    foreach(get_records($pdo,'crops') as $nr){$d=(string)($nr['expected_harvest_date']??'');if($d!==''){try{$dt=new DateTimeImmutable($d);$days=(int)$nToday->diff($dt)->format('%r%a');if($days<=30)$nHarvest[]=['name'=>$nr['crop_name']??'Crop','date'=>$d,'days'=>$days];}catch(Throwable $e){}}}
    foreach(get_records($pdo,'veterinary') as $nr){$d=(string)($nr['next_due_date']??'');if($d!==''){try{$dt=new DateTimeImmutable($d);$days=(int)$nToday->diff($dt)->format('%r%a');if($days<=30)$nVet[]=['animal'=>$nr['animal_type']??'Animal','id'=>$nr['animal_id']??'','medicine'=>$nr['medicine']??'Medicine','date'=>$d,'days'=>$days];}catch(Throwable $e){}}}
    foreach($nLow as $r)$notificationItems[]=['type'=>'Stock','icon'=>'📦','title'=>(string)($r['item_name']??'Item').' is low','detail'=>(string)($r['quantity']??0).' '.(string)($r['unit']??'').' remaining · minimum '.(string)($r['minimum_stock']??0)];
    foreach($nHarvest as $r)$notificationItems[]=['type'=>'Harvest','icon'=>'🌾','title'=>(string)$r['name'],'detail'=>($r['days']<0?'Overdue by '.abs($r['days']).' day(s)':($r['days']===0?'Due today':'Due in '.$r['days'].' day(s)')).' · '.(string)$r['date']];
    foreach($nVet as $r)$notificationItems[]=['type'=>'Veterinary','icon'=>'💊','title'=>(string)$r['medicine'].' — '.(string)$r['animal'],'detail'=>($r['days']<0?'Overdue by '.abs($r['days']).' day(s)':($r['days']===0?'Due today':'Due in '.$r['days'].' day(s)')).' · '.(string)$r['date']];
    if($page==='dashboard'){
        foreach($MODULES as $m=>$cfg)$stats[$m]=(int)$pdo->query("SELECT COUNT(*) FROM records WHERE module=".$pdo->quote($m))->fetchColumn();
        // Financial rules: Revenue = Sales totals only. Expenses = Procurement totals only.
        // Finance transactions are kept for the Finance module/reports but are not added to these two KPIs.
        $revenue=0.0; $expenses=0.0; $salesRows=get_records($pdo,'sales'); $procurementRows=get_records($pdo,'procurement'); $financeRows=get_records($pdo,'finance');
        foreach($salesRows as $r) $revenue += sale_total($r);
        foreach($procurementRows as $r) $expenses += procurement_total($r);
        foreach(get_records($pdo,'advisory') as $ar){ $revenue += clean_decimal($ar['amount_paid']??0); $expenses += clean_decimal($ar['project_cost']??0); }
        // Finance entries are shown in Group Finance; do not mix them into sales/procurement KPIs.
        $stats['_revenue']=$revenue; $stats['_expenses']=$expenses; $stats['_profit']=$revenue-$expenses;
    }
}
function nav_items(array $MODULES): array {
 return ['dashboard'=>['Executive Dashboard','⌂'],'crops'=>['Crop Farming','🌱'],'harvests'=>['Harvest','🌾'],'livestock'=>['Livestock','🐄'],'veterinary'=>['Veterinary','🩺'],'inventory'=>['Inventory','📦'],'stock_ledger'=>['Stock Ledger','📒'],'procurement'=>['Procurement','🛒'],'sales'=>['Sales','💰'],'finance'=>['Group Finance','💳'],'advisory'=>['Advisory Services','🤝'],'employees'=>['Employees','👥'],'equipment'=>['Equipment','🚜'],'fields'=>['Field Mapping','🗺️'],'customers'=>['Customers','👤'],'suppliers'=>['Suppliers','🏢'],'reports'=>['Reports','📊'],'ai_advisor'=>['AI Farm Advisor','✨'],'ai_assistant'=>['AI Assistant','💬'],'notifications'=>['Notifications','🔔'],'settings'=>['Settings','⚙️']];
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($page==='login'?'FWU Login':($page==='ai_advisor'?'AI Farm Advisor':($page==='ai_assistant'?'AI Assistant':($MODULES[$page]['label']??'Dashboard'))).' - Farming with Urunji')?></title>
<link rel="icon" href="assets/urunji-logo.png">
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="theme-<?=e($theme)?>">
<?php if($page==='login'): ?>
<div class="login-shell">
 <div class="login-card">
  <div class="brand big"><img src="assets/urunji-logo.png" onerror="this.style.display='none'"><div><b>Farming with Urunji</b><span>Farm Management System · Urunji Mezuwa</span></div></div>
  <h1>Sign in</h1><p class="muted">Access the shared business database from any authorised device.</p>
  <?php if(isset($dbError)): ?><div class="alert error">Database connection failed. Configure <b>config.php</b> first.</div><?php endif; ?>
  <?php if($flash): ?><div class="alert <?=$flash['type']?>"><?=e($flash['message'])?></div><?php endif; ?>
  <form method="post" class="form">
   <input type="hidden" name="action" value="login">
   <label>Username<input name="username" autocomplete="username" required></label>
   <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
   <button class="btn primary wide">Sign in</button>
  </form>
  <div class="login-note">All records are stored on MySQL. This application does not use browser LocalStorage as the source of truth.</div>
 </div>
</div>
<?php else: ?>
<div class="app">
<aside class="sidebar">
 <a class="brand brand-link" href="?page=dashboard" title="Go to Dashboard"><img src="assets/urunji-logo.png" onerror="this.style.display='none'"><div><b>Farming with Urunji</b><span>FWU</span></div></a>
 <nav><?php foreach(nav_items($MODULES) as $k=>$it): ?><a class="<?=$page===$k?'active':''?>" href="?page=<?=$k?>"><i><?=$it[1]?></i><?=$it[0]?></a><?php endforeach; ?></nav>
 <div class="side-bottom"><a class="website-link" href="https://web.facebook.com/profile.php?id=61555361564144/" target="_blank" rel="noopener">🌐 Visit Farming with Urunji Website</a><div class="user-mini"><b><?=e($_SESSION['username'])?></b><span><?=e($_SESSION['role'])?></span></div><a href="?logout=1">↪ Sign out</a></div>
</aside>
<main class="main">
<header class="topbar"><div><div class="eyebrow">FARMING WITH URUNJI · BUSINESS MANAGEMENT</div><h2><?=e($page==='ai_advisor'?'AI Farm Advisor':($MODULES[$page]['label']??'Dashboard'))?></h2></div><div class="top-actions"><a class="top-icon" href="?page=notifications" title="Notifications">🔔<?php if(count($notificationItems??[])): ?><sup><?=number_format(count($notificationItems))?></sup><?php endif; ?></a><form method="post" class="theme-form"><input type="hidden" name="_csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="save_theme"><input type="hidden" name="theme" value="<?=$theme==='dark'?'light':'dark'?>"><input type="hidden" name="return_page" value="<?=e($page)?>"><button class="top-icon" title="Toggle dark mode" type="submit"><?=$theme==='dark'?'☀️':'🌙'?></button></form><div class="top-user">● Workspace active · <?=e($_SESSION['username'])?></div></div></header>
<div class="content">
<?php if($flash): ?><div class="alert <?=$flash['type']?>"><?=e($flash['message'])?></div><?php endif; ?>

<?php if($page==='dashboard'): ?>
<?php
$lowStock=[]; $upcomingHarvests=[]; $vetDue=[]; $recent=[];
$chartPeriod=(string)($_GET['chart_period']??'this_year');
$chartRows=[];
$periodStart=null; $periodEnd=null;
if($chartPeriod==='this_month'){
    $periodStart=new DateTimeImmutable('first day of this month 00:00:00');
    $periodEnd=$periodStart->modify('first day of next month 00:00:00');
    for($d=1;$d<=(int)$periodStart->format('t');$d++) $chartRows[$d]=['label'=>(string)$d,'income'=>0.0,'expense'=>0.0];
}elseif($chartPeriod==='last_month'){
    $periodStart=new DateTimeImmutable('first day of last month 00:00:00');
    $periodEnd=$periodStart->modify('first day of this month 00:00:00');
    for($d=1;$d<=(int)$periodStart->format('t');$d++) $chartRows[$d]=['label'=>(string)$d,'income'=>0.0,'expense'=>0.0];
}elseif($chartPeriod==='all_time'){
    $periodStart=null; $periodEnd=null;
}else{
    $chartPeriod='this_year';
    $periodStart=new DateTimeImmutable('first day of January this year 00:00:00');
    $periodEnd=$periodStart->modify('first day of January next year 00:00:00');
    for($m=1;$m<=12;$m++) $chartRows[$m]=['label'=>date('M',mktime(0,0,0,$m,1)),'income'=>0.0,'expense'=>0.0];
}
$monthly=[];
foreach(get_records($pdo,'inventory') as $r){$q=clean_decimal($r['quantity']??0);$min=clean_decimal($r['minimum_stock']??0);if($q<=$min && $min>0)$lowStock[]=$r;}
$today=new DateTimeImmutable('today');
foreach(get_records($pdo,'crops') as $r){$d=(string)($r['expected_harvest_date']??'');if($d!==''){try{$dt=new DateTimeImmutable($d);$days=(int)$today->diff($dt)->format('%r%a');if($days<=30)$upcomingHarvests[]=['name'=>$r['crop_name']??'Crop','date'=>$d,'days'=>$days];}catch(Throwable $e){}}}
foreach(get_records($pdo,'veterinary') as $r){$d=(string)($r['next_due_date']??'');if($d!==''){try{$dt=new DateTimeImmutable($d);$days=(int)$today->diff($dt)->format('%r%a');if($days<=30)$vetDue[]=['animal'=>$r['animal_type']??'Animal','id'=>$r['animal_id']??'','medicine'=>$r['medicine']??'Medicine','date'=>$d,'days'=>$days];}catch(Throwable $e){}}}
$recent=$pdo->query("SELECT a.action_name,a.module,a.created_at,a.details_json,u.username FROM audit_log a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.id DESC LIMIT 8")->fetchAll();
// Financial chart: SALES revenue only; procurement + non-income finance are expenses.
// The chart can be filtered to this month, last month, this year, or all time.
if($chartPeriod==='all_time'){
    foreach($salesRows as $r){try{$dt=new DateTimeImmutable((string)($r['sale_date']??''));$key=$dt->format('Y-m');if(!isset($chartRows[$key]))$chartRows[$key]=['label'=>$dt->format('M Y'),'income'=>0.0,'expense'=>0.0];$chartRows[$key]['income']+=sale_total($r);}catch(Throwable $e){}}
    foreach($procurementRows as $r){try{$dt=new DateTimeImmutable((string)($r['purchase_date']??''));$key=$dt->format('Y-m');if(!isset($chartRows[$key]))$chartRows[$key]=['label'=>$dt->format('M Y'),'income'=>0.0,'expense'=>0.0];$chartRows[$key]['expense']+=procurement_total($r);}catch(Throwable $e){}}
    ksort($chartRows);
}elseif($chartPeriod==='this_month' || $chartPeriod==='last_month'){
    foreach($salesRows as $r){try{$dt=new DateTimeImmutable((string)($r['sale_date']??''));if($dt >= $periodStart && $dt < $periodEnd){$key=(int)$dt->format('j');$chartRows[$key]['income']+=sale_total($r);}}catch(Throwable $e){}}
    foreach($procurementRows as $r){try{$dt=new DateTimeImmutable((string)($r['purchase_date']??''));if($dt >= $periodStart && $dt < $periodEnd){$key=(int)$dt->format('j');$chartRows[$key]['expense']+=procurement_total($r);}}catch(Throwable $e){}}
}else{
    foreach($salesRows as $r){try{$dt=new DateTimeImmutable((string)($r['sale_date']??''));if($dt >= $periodStart && $dt < $periodEnd){$key=(int)$dt->format('n');$chartRows[$key]['income']+=sale_total($r);}}catch(Throwable $e){}}
    foreach($procurementRows as $r){try{$dt=new DateTimeImmutable((string)($r['purchase_date']??''));if($dt >= $periodStart && $dt < $periodEnd){$key=(int)$dt->format('n');$chartRows[$key]['expense']+=procurement_total($r);}}catch(Throwable $e){}}
}
// Advisory service receipts and direct project costs belong to the Shop venture.
foreach(get_records($pdo,'advisory') as $ar){
    $date=(string)(($ar['start_date']??'') ?: ($ar['due_date']??'')); if($date==='')continue;
    try{$dt=new DateTimeImmutable($date);$amountIn=clean_decimal($ar['amount_paid']??0);$amountOut=clean_decimal($ar['project_cost']??0);
      if($chartPeriod==='all_time'){$key=$dt->format('Y-m');if(!isset($chartRows[$key]))$chartRows[$key]=['label'=>$dt->format('M Y'),'income'=>0.0,'expense'=>0.0];$chartRows[$key]['income']+=$amountIn;$chartRows[$key]['expense']+=$amountOut;}
      elseif($dt >= $periodStart && $dt < $periodEnd){$key=($chartPeriod==='this_month'||$chartPeriod==='last_month')?(int)$dt->format('j'):(int)$dt->format('n');if(isset($chartRows[$key])){$chartRows[$key]['income']+=$amountIn;$chartRows[$key]['expense']+=$amountOut;}}
    }catch(Throwable $e){}
}
// Finance transactions are intentionally excluded from the dashboard expense chart.
if(!$chartRows){for($m=1;$m<=12;$m++)$chartRows[$m]=['label'=>date('M',mktime(0,0,0,$m,1)),'income'=>0.0,'expense'=>0.0];}
$maxChart=1;foreach($chartRows as $mm)$maxChart=max($maxChart,$mm['income'],$mm['expense']);
// Reconcile sellable farm stock by item name. Duplicate rows such as Goats / Livestock,
// Goats / Other and Goats / animals are treated as the same product and their quantities are added.
$stockDist=[];
foreach(get_records($pdo,'inventory') as $ir){
    $name=trim((string)($ir['item_name']??'')); if($name==='')continue;
    $key=strtolower($name);
    if(!isset($stockDist[$key]))$stockDist[$key]=['name'=>$name,'quantity'=>0.0,'units'=>[]];
    $stockDist[$key]['quantity']+=clean_decimal($ir['quantity']??0);
    $unit=trim((string)($ir['unit']??'')); if($unit!=='')$stockDist[$key]['units'][$unit]=true;
}
usort($stockDist,fn($a,$b)=>$b['quantity']<=>$a['quantity']);
$stockTotal=array_sum(array_column($stockDist,'quantity'));
$notificationItems=[]; foreach($lowStock as $r)$notificationItems[]=['type'=>'Stock','icon'=>'📦','title'=>(string)($r['item_name']??'Item').' is low','detail'=>(string)($r['quantity']??0).' '.(string)($r['unit']??'').' remaining · minimum '.(string)($r['minimum_stock']??0)]; foreach($upcomingHarvests as $r)$notificationItems[]=['type'=>'Harvest','icon'=>'🌾','title'=>(string)$r['name'],'detail'=>($r['days']<0?'Overdue by '.abs($r['days']).' day(s)':($r['days']===0?'Due today':'Due in '.$r['days'].' day(s)')).' · '.(string)$r['date']]; foreach($vetDue as $r)$notificationItems[]=['type'=>'Veterinary','icon'=>'💊','title'=>(string)$r['medicine'].' — '.(string)$r['animal'],'detail'=>($r['days']<0?'Overdue by '.abs($r['days']).' day(s)':($r['days']===0?'Due today':'Due in '.$r['days'].' day(s)')).' · '.(string)$r['date']];
?>
<section class="hero"><div><span class="pill">EXECUTIVE OVERVIEW</span><h1>Four business areas. One clear picture.</h1><p>Monitor business performance, farm operations, stock levels, upcoming tasks and recent activity from one place. Figures and alerts reflect records saved in your system.</p></div><div class="sync-box"><b>Today’s Snapshot</b><br><small>🛒 <?=number_format($stats['sales']??0)?> sales records<br>🌱 <?=number_format($stats['crops']??0)?> crop records<br>🐄 <?=number_format($stats['livestock']??0)?> livestock records<br>🔔 <?=number_format(count($notificationItems))?> active alerts</small></div></section>
<div class="venture-overview-grid">
<?php
$ventureOverview=[
 ['key'=>'shop','title'=>'Shop','subtitle'=>'Retail stock, purchases and sales','icon'=>'🏪','modules'=>['inventory','sales','procurement']],
 ['key'=>'crops','title'=>'Beans & Onions Farm','subtitle'=>'Beans and onions production, fields and harvests','icon'=>'🌱','modules'=>['crops','harvests','fields']],
 ['key'=>'cattle','title'=>'Cattle Farm','subtitle'=>'Herd, health and veterinary records','icon'=>'🐄','modules'=>['livestock','veterinary']]
];
foreach($ventureOverview as $vo): $vc=0; foreach($vo['modules'] as $vm) foreach(get_records($pdo,$vm) as $vr) if(($vr['venture']??($vm==='livestock'||$vm==='veterinary'?'cattle':($vm==='crops'||$vm==='harvests'||$vm==='fields'?'crops':($vm==='advisory'?'advisory':'shop'))))===$vo['key'])$vc++; ?>
<a class="venture-overview-card" href="?page=finance&venture=<?=e($vo['key'])?>"><span class="venture-icon"><?=e($vo['icon'])?></span><div><b><?=e($vo['title'])?></b><small><?=e($vo['subtitle'])?></small><strong><?=number_format($vc)?> operational records</strong></div><span class="venture-arrow">→</span></a>
<?php endforeach; ?>
<a class="venture-overview-card group-card" href="?page=finance&venture=all"><span class="venture-icon">📊</span><div><b>Whole Business Group</b><small>Combined finance and management</small><strong>View consolidated overview</strong></div><span class="venture-arrow">→</span></a>
</div>
<div class="cards"><?php $cardmods=['inventory','livestock','crops','harvests','procurement','sales','employees','equipment']; foreach($cardmods as $m): ?><a class="stat" href="?page=<?=$m?>"><span><?=$MODULES[$m]['icon']?></span><b><?=number_format($stats[$m]??0)?></b><small><?=$MODULES[$m]['label']?> records</small></a><?php endforeach;?></div>
<?php
// Venture-specific dashboard charts. Every sale and procurement is attributed to its saved venture.
$ventureChartDefs=[
 ['key'=>'shop','title'=>'Shop Performance','subtitle'=>'Shop sales and shop procurement only','icon'=>'🏪'],
 ['key'=>'crops','title'=>'Beans & Onions Farm Performance','subtitle'=>'Beans/onions farm sales and procurement only','icon'=>'🌱'],
 ['key'=>'cattle','title'=>'Cattle Farm Performance','subtitle'=>'Cattle sales and cattle procurement only','icon'=>'🐄']
];
$ventureChartData=[];
foreach($ventureChartDefs as $def){
 $rows=$chartRows;
 foreach($rows as &$row){$row['income']=0.0;$row['expense']=0.0;} unset($row);
 foreach($salesRows as $r){if((string)($r['venture']??'shop')!==$def['key'])continue;try{$dt=new DateTimeImmutable((string)($r['sale_date']??''));if($chartPeriod==='all_time'){$key=$dt->format('Y-m');if(!isset($rows[$key]))$rows[$key]=['label'=>$dt->format('M Y'),'income'=>0.0,'expense'=>0.0];$rows[$key]['income']+=sale_total($r);}elseif($dt >= $periodStart && $dt < $periodEnd){$key=($chartPeriod==='this_month'||$chartPeriod==='last_month')?(int)$dt->format('j'):(int)$dt->format('n');if(isset($rows[$key]))$rows[$key]['income']+=sale_total($r);}}catch(Throwable $e){}}
 foreach($procurementRows as $r){if((string)($r['venture']??'shop')!==$def['key'])continue;try{$dt=new DateTimeImmutable((string)($r['purchase_date']??''));if($chartPeriod==='all_time'){$key=$dt->format('Y-m');if(!isset($rows[$key]))$rows[$key]=['label'=>$dt->format('M Y'),'income'=>0.0,'expense'=>0.0];$rows[$key]['expense']+=procurement_total($r);}elseif($dt >= $periodStart && $dt < $periodEnd){$key=($chartPeriod==='this_month'||$chartPeriod==='last_month')?(int)$dt->format('j'):(int)$dt->format('n');if(isset($rows[$key]))$rows[$key]['expense']+=procurement_total($r);}}catch(Throwable $e){}}
 if($def['key']==='shop')foreach(get_records($pdo,'advisory') as $ar){$date=(string)(($ar['start_date']??'') ?: ($ar['due_date']??''));if($date==='')continue;try{$dt=new DateTimeImmutable($date);if($chartPeriod==='all_time'){$key=$dt->format('Y-m');if(!isset($rows[$key]))$rows[$key]=['label'=>$dt->format('M Y'),'income'=>0.0,'expense'=>0.0];$rows[$key]['income']+=clean_decimal($ar['amount_paid']??0);$rows[$key]['expense']+=clean_decimal($ar['project_cost']??0);}elseif($dt >= $periodStart && $dt < $periodEnd){$key=($chartPeriod==='this_month'||$chartPeriod==='last_month')?(int)$dt->format('j'):(int)$dt->format('n');if(isset($rows[$key])){$rows[$key]['income']+=clean_decimal($ar['amount_paid']??0);$rows[$key]['expense']+=clean_decimal($ar['project_cost']??0);}}}catch(Throwable $e){}}
 ksort($rows);$ventureChartData[$def['key']]=['def'=>$def,'rows'=>array_values($rows)];
}
?>
<div class="venture-chart-stack">
<?php foreach($ventureChartData as $vc): $vals=$vc['rows'];$maxV=1;foreach($vals as $v)$maxV=max($maxV,$v['income'],$v['expense']);$n=max(1,count($vals));$left=58;$right=790;$top=22;$bottom=220;$height=$bottom-$top;$width=$right-$left;$ip=[];$ep=[];foreach($vals as $i=>$v){$x=$n===1?($left+$width/2):($left+($i/($n-1))*$width);$ip[]=round($x,2).','.round($bottom-($v['income']/$maxV)*$height,2);$ep[]=round($x,2).','.round($bottom-($v['expense']/$maxV)*$height,2);} ?>
 <section class="panel chart-box venture-chart"><div class="chart-title"><div><b><?=e($vc['def']['icon'].' '.$vc['def']['title'])?></b><small><?=e($vc['def']['subtitle'])?></small></div></div>
 <?php $ventureIncome=array_sum(array_column($vals,'income'));$ventureExpense=array_sum(array_column($vals,'expense')); ?>
 <div class="venture-chart-kpis"><div><small>Income in selected period</small><b>MWK <?=number_format($ventureIncome,2)?></b></div><div><small>Expenses in selected period</small><b>MWK <?=number_format($ventureExpense,2)?></b></div><div><small>Net recorded result</small><b>MWK <?=number_format($ventureIncome-$ventureExpense,2)?></b></div></div>
 <div class="line-chart-wrap"><svg class="financial-line-chart" viewBox="0 0 820 270" role="img" aria-label="<?=e($vc['def']['title'])?> sales and procurement chart">
 <?php for($g=0;$g<=4;$g++):$y=$bottom-($g/4)*$height;$amount=$maxV*$g/4; ?><line x1="<?=$left?>" y1="<?=$y?>" x2="<?=$right?>" y2="<?=$y?>" class="chart-gridline"/><text x="<?=$left-8?>" y="<?=$y+4?>" text-anchor="end" class="chart-axis-label"><?=e(number_format($amount,0))?></text><?php endfor; ?>
 <polyline points="<?=e(implode(' ',$ip))?>" class="chart-line chart-line-revenue"><title>Sales and service income. Hover over a point for the exact amount.</title></polyline><polyline points="<?=e(implode(' ',$ep))?>" class="chart-line chart-line-expense"><title>Procurement and service costs. Hover over a point for the exact amount.</title></polyline>
 <?php foreach($vals as $i=>$v):$x=$n===1?($left+$width/2):($left+($i/($n-1))*$width);$iy=$bottom-($v['income']/$maxV)*$height;$ey=$bottom-($v['expense']/$maxV)*$height; ?><circle cx="<?=$x?>" cy="<?=$iy?>" r="6" class="chart-point chart-point-revenue"><title><?=e($v['label'])?> income: MWK <?=e(number_format($v['income'],2))?></title></circle><circle cx="<?=$x?>" cy="<?=$ey?>" r="6" class="chart-point chart-point-expense"><title><?=e($v['label'])?> expenses: MWK <?=e(number_format($v['expense'],2))?></title></circle><text x="<?=$x?>" y="245" text-anchor="middle" class="chart-axis-label"><?=e((string)$v['label'])?></text><?php endforeach; ?>
 </svg></div><div class="chart-legend"><span><i class="dot income"></i> Sales & service income</span><span><i class="dot expense"></i> Procurement & service costs</span></div></section>
<?php endforeach; ?>
</div>
<div class="finance-grid">
<div class="panel"><h3>Financial overview</h3><div class="money-row"><div><span>All-time revenue</span><b>MWK <?=number_format($stats['_revenue']??0,2)?></b></div><div><span>All-time expenses</span><b>MWK <?=number_format($stats['_expenses']??0,2)?></b></div><div><span>All-time net</span><b>MWK <?=number_format($stats['_profit']??0,2)?></b></div></div>
<div class="chart-box">
<div class="chart-title"><div><b>Overall Business Group</b><small>Combined sales and advisory income vs procurement and advisory costs across all ventures</small></div><form method="get" class="chart-filter"><input type="hidden" name="page" value="dashboard"><select name="chart_period" onchange="this.form.submit()"><option value="this_month" <?=$chartPeriod==='this_month'?'selected':''?>>This month</option><option value="last_month" <?=$chartPeriod==='last_month'?'selected':''?>>Last month</option><option value="this_year" <?=$chartPeriod==='this_year'?'selected':''?>>This year</option><option value="all_time" <?=$chartPeriod==='all_time'?'selected':''?>>All time</option></select></form></div>
<?php
$chartValues=array_values($chartRows);$chartCount=max(1,count($chartValues));$plotLeft=58;$plotRight=790;$plotTop=22;$plotBottom=236;$plotHeight=$plotBottom-$plotTop;$plotWidth=$plotRight-$plotLeft;$axisMax=max(1,$maxChart);$incomePoints=[];$expensePoints=[];
foreach($chartValues as $i=>$v){$x=$chartCount===1?($plotLeft+$plotWidth/2):($plotLeft+($i/($chartCount-1))*$plotWidth);$iy=$plotBottom-($v['income']/$axisMax)*$plotHeight;$ey=$plotBottom-($v['expense']/$axisMax)*$plotHeight;$incomePoints[]=[$x,$iy,$v];$expensePoints[]=[$x,$ey,$v];}
$toPolyline=function(array $points):string{return implode(' ',array_map(fn($p)=>round($p[0],2).','.round($p[1],2),$points));};
?>
<div class="line-chart-wrap"><svg class="financial-line-chart" viewBox="0 0 820 292" role="img" aria-label="Monthly sales revenue and procurement expenses line chart">
<?php for($g=0;$g<=5;$g++):$y=$plotBottom-($g/5)*$plotHeight;$value=$axisMax*$g/5; ?><line x1="<?=$plotLeft?>" y1="<?=$y?>" x2="<?=$plotRight?>" y2="<?=$y?>" class="chart-gridline"/><text x="<?=$plotLeft-8?>" y="<?=$y+4?>" text-anchor="end" class="chart-axis-label"><?=e(number_format($value,0))?></text><?php endfor; ?>
<polyline points="<?=$toPolyline($incomePoints)?>" class="chart-line chart-line-revenue"><title>Sales and service income. Hover over a point to see its exact amount.</title></polyline><polyline points="<?=$toPolyline($expensePoints)?>" class="chart-line chart-line-expense"><title>Procurement and service costs. Hover over a point to see its exact amount.</title></polyline>
<?php foreach($incomePoints as $pt): ?><circle cx="<?=$pt[0]?>" cy="<?=$pt[1]?>" r="6" class="chart-point chart-point-revenue"><title><?=e($pt[2]['label'])?> sales: MWK <?=e(number_format($pt[2]['income'],2))?></title></circle><?php endforeach; ?>
<?php foreach($expensePoints as $pt): ?><circle cx="<?=$pt[0]?>" cy="<?=$pt[1]?>" r="6" class="chart-point chart-point-expense"><title><?=e($pt[2]['label'])?> procurement: MWK <?=e(number_format($pt[2]['expense'],2))?></title></circle><?php endforeach; ?>
<?php foreach($chartValues as $i=>$v):$x=$chartCount===1?($plotLeft+$plotWidth/2):($plotLeft+($i/($chartCount-1))*$plotWidth); ?><text x="<?=$x?>" y="260" text-anchor="middle" class="chart-axis-label"><?=e((string)$v['label'])?></text><?php endforeach; ?>
</svg></div>
<div class="chart-legend"><span><i class="dot income"></i> Sales & service income</span><span><i class="dot expense"></i> Procurement & service costs</span></div>
</div></div>

<div class="panel chart-box livestock-chart">
<div class="chart-title"><b>Livestock Distribution</b><small>Animals grouped by type</small></div>
<?php
$animalDist=[];foreach(get_records($pdo,'livestock') as $animal){$animalName=trim((string)($animal['animal_type']??''));if($animalName==='')$animalName='Unspecified';$key=strtolower($animalName);if(!isset($animalDist[$key]))$animalDist[$key]=['name'=>$animalName,'count'=>0];$animalDist[$key]['count']++;}
usort($animalDist,fn($a,$b)=>$b['count']<=>$a['count']);$animalTotal=array_sum(array_column($animalDist,'count'));
$animalColors=['#08a6e8','#ff5c83','#ff9e45','#7d72e8','#49b88a','#d6a566','#8e9aa8','#d9bf35'];$circ=2*pi()*68;$dashOffset=0;
?>
<?php if(!$animalDist): ?><div class="chart-empty">No livestock records available yet.</div><?php else: ?>
<div class="animal-donut-area"><svg class="animal-donut" viewBox="0 0 200 200" role="img" aria-label="Livestock distribution doughnut chart">
<circle cx="100" cy="100" r="68" class="donut-track"/>
<?php foreach($animalDist as $i=>$animal):$length=$animalTotal>0?($animal['count']/$animalTotal)*$circ:0;$color=$animalColors[$i%count($animalColors)]; ?><circle cx="100" cy="100" r="68" fill="none" stroke="<?=$color?>" stroke-width="30" stroke-dasharray="<?=$length?> <?=$circ-$length?>" stroke-dashoffset="<?=-$dashOffset?>" transform="rotate(-90 100 100)"><title><?=e($animal['name'])?>: <?=$animal['count']?> animals</title></circle><?php $dashOffset+=$length;endforeach; ?>
<text x="100" y="95" class="donut-total"><?=number_format($animalTotal)?></text><text x="100" y="114" class="donut-subtitle">ANIMALS</text></svg></div>
<div class="pie-legend animal-legend"><?php foreach($animalDist as $i=>$animal): ?><div><i style="background:<?=$animalColors[$i%count($animalColors)]?>"></i><span><?=e($animal['name'])?></span><b><?=number_format($animal['count'])?> (<?=number_format($animal['count']/$animalTotal*100,1)?>%)</b></div><?php endforeach; ?></div>
<?php endif; ?>
</div></div>

<?php $dashboardInsights=ai_advisor_insights($pdo); ?>
<div class="panel ai-preview"><div class="list-head"><div><h3>✨ AI Farm Advisor</h3><p class="muted">Database-driven risks and suggested next steps</p></div><a class="btn primary" href="?page=ai_advisor">View insights</a></div><?php foreach(array_slice($dashboardInsights,0,2) as $ins): ?><div class="ai-insight-row"><span><?=e($ins['icon'])?></span><div><b><?=e($ins['title'])?></b><small><?=e($ins['detail'])?></small></div><span class="ai-level <?=e($ins['level'])?>"><?=e(strtoupper($ins['level']))?></span></div><?php endforeach; ?></div>
<div class="panel"><h3>Farm attention</h3><ul class="checks"><li>✓ Server-side sessions</li><li>✓ Transactional stock updates</li><li>✓ Audit log for changes</li><li>✓ Multi-device reads from one database</li><li><?=count($lowStock) ? '⚠ '.number_format(count($lowStock)).' low-stock item(s)' : '✓ No low-stock items'?></li><li><?=count($vetDue) ? '⚠ '.number_format(count($vetDue)).' veterinary reminder(s) within 30 days' : '✓ No veterinary reminders within 30 days'?></li></ul></div>
<div class="dashboard-grid">
<div class="panel"><h3>Inventory alerts</h3><?php if(!$lowStock): ?><p class="muted">No items are currently at or below their minimum stock level.</p><?php else: ?><ul class="activity-list"><?php foreach(array_slice($lowStock,0,7) as $r): ?><li><span>📦</span><div><b><?=e($r['item_name']??'Item')?></b><small><?=e($r['quantity']??0)?> <?=e($r['unit']??'')?> remaining · minimum <?=e($r['minimum_stock']??0)?></small></div></li><?php endforeach;?></ul><?php endif;?></div>
<div class="panel"><h3>Upcoming harvests</h3><?php usort($upcomingHarvests,fn($a,$b)=>$a['days']<=>$b['days']); if(!$upcomingHarvests): ?><p class="muted">No harvest dates within the next 30 days.</p><?php else: ?><ul class="activity-list"><?php foreach(array_slice($upcomingHarvests,0,7) as $r): ?><li><span>🌾</span><div><b><?=e($r['name'])?></b><small><?= $r['days']<0 ? 'Overdue by '.abs($r['days']).' day(s)' : ($r['days']===0 ? 'Due today' : 'Due in '.$r['days'].' day(s)') ?> · <?=e($r['date'])?></small></div></li><?php endforeach;?></ul><?php endif;?></div>
<div class="panel"><h3>Veterinary reminders</h3><?php usort($vetDue,fn($a,$b)=>$a['days']<=>$b['days']); if(!$vetDue): ?><p class="muted">No veterinary treatments due within the next 30 days.</p><?php else: ?><ul class="activity-list"><?php foreach(array_slice($vetDue,0,7) as $r): ?><li><span>💊</span><div><b><?=e($r['medicine'])?> — <?=e($r['animal'])?> <?=e($r['id'])?></b><small><?= $r['days']<0 ? 'Overdue by '.abs($r['days']).' day(s)' : ($r['days']===0 ? 'Due today' : 'Due in '.$r['days'].' day(s)') ?> · <?=e($r['date'])?></small></div></li><?php endforeach;?></ul><?php endif;?></div>
<div class="panel"><h3>Recent activity</h3><?php if(!$recent): ?><p class="muted">No activity has been recorded yet.</p><?php else: ?><ul class="activity-list"><?php foreach($recent as $r): ?><li><span>•</span><div><b><?=e($r['action_name'])?> <?=e($r['module']??'system')?></b><small><?=e($r['username']??'System')?> · <?=e($r['created_at'])?></small></div></li><?php endforeach;?></ul><?php endif;?></div>
</div>
<div class="panel quick-panel"><h3>Quick actions</h3><div class="quick-actions"><a class="btn primary" href="?page=crops">＋ Crop</a><a class="btn primary" href="?page=finance">＋ Expense / Income</a><a class="btn primary" href="?page=employees">＋ Employee</a><a class="btn primary" href="?page=harvests">＋ Harvest</a><a class="btn primary" href="?page=sales">＋ Sale</a><a class="btn primary" href="?page=inventory">＋ Inventory</a></div></div><div class="panel quick-panel"><div class="list-head"><div><h3>Notifications</h3><p class="muted"><?=number_format(count($notificationItems))?> active farm alerts</p></div><a class="btn" href="?page=notifications">View all</a></div><?php if(!$notificationItems): ?><p class="muted">✓ No active notifications.</p><?php else: ?><ul class="activity-list"><?php foreach(array_slice($notificationItems,0,6) as $n): ?><li><span><?=e($n['icon'])?></span><div><b><?=e($n['title'])?></b><small><?=e($n['detail'])?></small></div></li><?php endforeach; ?></ul><?php endif; ?></div>

<?php elseif($page==='stock_ledger'): ?>
<?php $ledger=$pdo->query("SELECT item_key,item_name,movement_type,quantity,reference_module,notes,created_at FROM stock_ledger ORDER BY id DESC LIMIT 500")->fetchAll(); if(isset($_GET['export'])&&$_GET['export']==='csv'){header('Content-Type:text/csv; charset=utf-8');header('Content-Disposition:attachment; filename="FWU-stock-ledger-'.date('Y-m-d').'.csv"');$out=fopen('php://output','w');fputcsv($out,['Date','Venture','Item','Movement','Quantity','Reference','Notes']);foreach($ledger as $lr)fputcsv($out,[$lr['created_at'],explode('|',(string)$lr['item_key'])[0]??'shop',$lr['item_name'],$lr['movement_type'],$lr['quantity'],$lr['reference_module'],$lr['notes']]);fclose($out);exit;} ?>
<div class="panel"><div class="list-head"><div><h3>Stock Ledger</h3><p class="muted">Central MySQL stock movements from procurement and sales.</p></div><a class="btn primary" href="?page=stock_ledger&export=csv">⬇ Export CSV</a></div><div class="table-wrap"><table><thead><tr><th>Date</th><th>Venture</th><th>Item</th><th>Movement</th><th>Quantity</th><th>Reference</th><th>Notes</th></tr></thead><tbody><?php if(!$ledger): ?><tr><td colspan="7" class="empty">No stock movements recorded yet.</td></tr><?php else: foreach($ledger as $lr): ?><tr><td><?=e($lr['created_at'])?></td><td><?=e(ucfirst(explode('|',(string)($lr['item_key']??'shop'))[0]??'shop'))?></td><td><?=e($lr['item_name'])?></td><td><span class="movement <?=strtolower((string)$lr['movement_type'])==='in'?'movement-in':'movement-out'?>"><?=e($lr['movement_type'])?></span></td><td><?=number_format((float)$lr['quantity'],3)?></td><td><?=e($lr['reference_module']??'')?></td><td><?=e($lr['notes']??'')?></td></tr><?php endforeach;endif;?></tbody></table></div></div>

<?php elseif($page==='ai_advisor'): ?>
<?php $advisorInsights=ai_advisor_insights($pdo); ?>
<div class="hero ai-hero"><div><span class="pill">FARM DECISION SUPPORT</span><h1>AI Farm Advisor</h1><p>Practical insights generated from your saved farm records. Each finding explains the signal and suggests a next step. These rules support decisions; they do not replace field inspections, veterinary professionals or formal accounting.</p></div><div class="sync-box">✨ Live analysis<br><small>Insights are recalculated from MySQL whenever this page loads. No external AI service or additional data store is required.</small></div></div>
<div class="ai-disclaimer">This first version uses transparent rule-based intelligence, not a generative AI model. Estimates are only shown where the stored records support them.</div>
<div class="ai-insights-grid"><?php foreach($advisorInsights as $ins): ?><article class="panel ai-insight-card"><div class="ai-card-top"><span class="ai-icon"><?=e($ins['icon'])?></span><span class="ai-level <?=e($ins['level'])?>"><?=e(strtoupper($ins['level']))?></span></div><h3><?=e($ins['title'])?></h3><p><?=e($ins['detail'])?></p><div class="ai-next"><b>Recommended next step</b><p><?=e($ins['action'])?></p></div><a class="btn" href="?page=<?=e($ins['link'])?>">Open related module →</a></article><?php endforeach; ?></div>
<?php $forecast=ai_forecast_metrics($pdo); ?>
<div class="ai-forecast-grid">
 <section class="panel"><div class="list-head"><div><h3>📈 Financial outlook</h3><p class="muted">Simple baseline using the last three calendar months, including months with no recorded activity.</p></div></div>
 <?php if($forecast['history_months']<3): ?><p class="muted">Not enough historical activity yet for a useful three-month baseline. Continue recording dated sales and procurement.</p><?php else: ?><div class="forecast-number-grid"><div class="forecast-number"><small>Indicative monthly sales average</small><b>MWK <?=number_format($forecast['sales_forecast'],2)?></b></div><div class="forecast-number"><small>Indicative monthly procurement average</small><b>MWK <?=number_format($forecast['expense_forecast'],2)?></b></div></div><p class="muted">This is a simple baseline, not a guaranteed forecast. Seasonality, future prices and incomplete records are not modelled.</p><?php endif; ?>
 <div class="table-wrap"><table><thead><tr><th>Month</th><th>Sales (MWK)</th><th>Procurement (MWK)</th></tr></thead><tbody><?php foreach($forecast['months'] as $m): ?><tr><td><?=e($m['label'])?></td><td><?=number_format($m['sales'],2)?></td><td><?=number_format($m['procurement'],2)?></td></tr><?php endforeach; ?></tbody></table></div>
 </section>
 <section class="panel"><div class="list-head"><div><h3>📦 Stock depletion estimates</h3><p class="muted">Based on recorded stock-ledger OUT movements over 90 days.</p></div></div>
 <?php if(!$forecast['stock_forecast']): ?><p class="muted">No recent stock-out history is available to estimate stock duration. Continue recording stock movements.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Item</th><th>On hand</th><th>Estimated days left</th></tr></thead><tbody><?php foreach($forecast['stock_forecast'] as $sf): ?><tr><td><?=e($sf['name'])?></td><td><?=number_format($sf['quantity'],2).' '.e($sf['unit'])?></td><td><?php if($sf['days']===null): ?>—<?php else: ?><?=number_format($sf['days'],1)?> days<?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div><p class="muted">These are rough estimates assuming recent usage continues. Items with zero recorded use are excluded.</p><?php endif; ?>
 </section>
</div>
<div class="panel ai-method"><h3>How the advisor works</h3><ul><li>Compares sales totals with procurement totals using the system's existing calculation rules.</li><li>Checks inventory quantities against configured minimum stock levels.</li><li>Flags expected harvest dates and veterinary due dates that are overdue or approaching.</li><li>Highlights livestock health statuses explicitly recorded as sick, ill, poor or injured.</li><li>Identifies missing expected harvest dates that limit planning quality.</li></ul><p class="muted">Recommendations depend on the completeness and accuracy of the records. Review the source record before acting.</p></div>

<?php elseif($page==='ai_assistant'): ?>
<div class="hero ai-hero"><div><span class="pill">NO-COST FARM DATA ASSISTANT</span><h1>AI Farm Assistant</h1><p>Ask questions about the records saved in your farm system. This first version uses local, rule-based question matching and calculations—no external AI API or subscription.</p></div><div class="sync-box">🔒 Private by design<br><small>Answers are calculated from this system's MySQL records after login. Unsupported questions are not guessed.</small></div></div>
<div class="ai-disclaimer">For accuracy, the assistant distinguishes recorded totals from estimates. Procurement-versus-sales difference is not full accounting profit; forecasts are indicative and depend on complete records.</div>
<div class="panel ai-chat-panel"><h3>Ask about your farm</h3><form method="post" class="ai-question-form"><input type="hidden" name="_csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="ask_ai"><label for="ai-question">Your question</label><textarea id="ai-question" name="question" rows="3" maxlength="500" required placeholder="Example: Which inventory items are low?" ><?=e((string)($_SESSION['ai_last_question']??''))?></textarea><div class="form-actions"><button class="btn primary" type="submit">Analyse farm records</button><a class="btn" href="?page=ai_assistant&clear=1">Clear</a></div></form>
<?php if(!empty($_SESSION['ai_last_answer'])&&!isset($_GET['clear'])): ?><div class="ai-answer"><span class="pill">RESULT FROM YOUR RECORDS</span><p><?=nl2br(e((string)$_SESSION['ai_last_answer']))?></p><small>Generated <?=e(date('Y-m-d H:i'))?> · Verify important figures against the source module.</small></div><?php endif; ?>
<div class="ai-suggestions"><b>Try one of these questions</b><div class="ai-suggestion-list"><button type="button" class="btn" data-ai-question="How much are my sales?">Sales revenue</button><button type="button" class="btn" data-ai-question="Which stock items are low?">Low stock</button><button type="button" class="btn" data-ai-question="What harvests are due?">Upcoming harvests</button><button type="button" class="btn" data-ai-question="Which veterinary visits are overdue?">Veterinary follow-ups</button><button type="button" class="btn" data-ai-question="How many livestock do I have?">Livestock count</button><button type="button" class="btn" data-ai-question="Summarise procurement expenses">Procurement summary</button></div></div></div>
<script>document.querySelectorAll('[data-ai-question]').forEach(function(b){b.addEventListener('click',function(){var t=document.getElementById('ai-question');if(t){t.value=b.getAttribute('data-ai-question')||'';t.focus();}})});</script>
<?php elseif($page==='reports'): ?>
<?php
$reportType=(string)($_GET['report_type']??'financial'); $startDate=(string)($_GET['start_date']??''); $endDate=(string)($_GET['end_date']??''); $reportVenture=(string)($_GET['venture']??'all'); if(!in_array($reportVenture,['all','shop','crops','cattle'],true))$reportVenture='all';
$reportRows=[];$reportTitle='Financial Report';$reportIncome=0.0;$reportExpenses=0.0;$reportCount=0;
$inRange=function(string $d)use($startDate,$endDate):bool{ if($d==='')return false; if($startDate!==''&&$d<$startDate)return false; if($endDate!==''&&$d>$endDate)return false; return true; };
if($reportType==='sales'){ $reportTitle='Sales Revenue Report'; foreach(get_records($pdo,'sales') as $r){$d=(string)($r['sale_date']??'');if(!$inRange($d)||($reportVenture!=='all'&&canonical_venture($r['venture']??'shop','sales')!==$reportVenture))continue;$total=sale_total($r);$reportIncome+=$total;$reportCount++;$reportRows[]=['date'=>$d,'description'=>(string)($r['item_name']??'Sale'),'category'=>(string)($r['customer']??''),'amount'=>$total,'type'=>'Sales Revenue'];} }
elseif($reportType==='expenses'){ $reportTitle='Procurement & Service Expense Report'; foreach(get_records($pdo,'procurement') as $r){$d=(string)($r['purchase_date']??'');if(!$inRange($d)||($reportVenture!=='all'&&canonical_venture($r['venture']??'shop','procurement')!==$reportVenture))continue;$total=procurement_total($r);$reportExpenses+=$total;$reportCount++;$reportRows[]=['date'=>$d,'description'=>(string)($r['item_name']??'Procurement'),'category'=>'Procurement','amount'=>$total,'type'=>'Procurement Expense'];} if($reportVenture==='all'||$reportVenture==='shop')foreach(get_records($pdo,'advisory') as $ar){$d=(string)(($ar['start_date']??'')?:($ar['due_date']??''));if(!$inRange($d))continue;$cost=clean_decimal($ar['project_cost']??0);if($cost>0){$reportExpenses+=$cost;$reportCount++;$reportRows[]=['date'=>$d,'description'=>'Advisory project cost: '.(string)($ar['engagement_title']??$ar['client_name']??'Service'),'category'=>'Advisory Services','amount'=>$cost,'type'=>'Advisory Expense'];}} }
else { $reportTitle='Financial Summary'; foreach(get_records($pdo,'sales') as $r){$d=(string)($r['sale_date']??'');if(!$inRange($d)||($reportVenture!=='all'&&canonical_venture($r['venture']??'shop','sales')!==$reportVenture))continue;$total=sale_total($r);$reportIncome+=$total;$reportCount++;$reportRows[]=['date'=>$d,'description'=>(string)($r['item_name']??'Sale'),'category'=>(string)($r['customer']??''),'amount'=>$total,'type'=>'Sales Revenue'];} foreach(get_records($pdo,'procurement') as $r){$d=(string)($r['purchase_date']??'');if(!$inRange($d)||($reportVenture!=='all'&&canonical_venture($r['venture']??'shop','procurement')!==$reportVenture))continue;$total=procurement_total($r);$reportExpenses+=$total;$reportCount++;$reportRows[]=['date'=>$d,'description'=>(string)($r['item_name']??'Procurement'),'category'=>'Procurement','amount'=>$total,'type'=>'Procurement Expense'];}
foreach(get_records($pdo,'advisory') as $ar){if($reportVenture!=='all'&&$reportVenture!=='shop')continue;$d=(string)(($ar['start_date']??'')?:($ar['due_date']??''));if(!$inRange($d))continue;$paid=clean_decimal($ar['amount_paid']??0);$cost=clean_decimal($ar['project_cost']??0);if($paid>0){$reportIncome+=$paid;$reportCount++;$reportRows[]=['date'=>$d,'description'=>'Advisory service: '.(string)($ar['engagement_title']??$ar['client_name']??'Service'),'category'=>(string)($ar['client_name']??''),'amount'=>$paid,'type'=>'Advisory Service Income'];}if($cost>0){$reportExpenses+=$cost;$reportCount++;$reportRows[]=['date'=>$d,'description'=>'Advisory project cost: '.(string)($ar['engagement_title']??$ar['client_name']??'Service'),'category'=>'Advisory Services','amount'=>$cost,'type'=>'Advisory Expense'];}}
} 
usort($reportRows,fn($a,$b)=>strcmp($b['date'],$a['date']));
if(isset($_GET['export'])&&$_GET['export']==='csv'){header('Content-Type:text/csv; charset=utf-8');header('Content-Disposition:attachment; filename="FWU-'.preg_replace('/[^A-Za-z0-9]+/','-',strtolower($reportType)).'-report-'.date('Y-m-d').'.csv"');$out=fopen('php://output','w');fputcsv($out,['Date','Type','Description','Category','Amount (MWK)']);foreach($reportRows as $rr)fputcsv($out,[$rr['date'],$rr['type'],$rr['description'],$rr['category'],number_format($rr['amount'],2,'.','')]);fclose($out);exit;}
?>
<div class="panel report-generator"><div class="list-head"><div><h3><?=e($reportTitle)?></h3><p class="muted">Generate reports directly from the shared MySQL data. Revenue = Sales plus recorded advisory receipts; expenses = Procurement plus recorded advisory project costs in the financial summary.</p></div><div class="report-actions"><button class="btn" onclick="window.print()">🖨 Print</button><a class="btn" href="?page=reports&report_type=<?=e($reportType)?>&start_date=<?=e($startDate)?>&end_date=<?=e($endDate)?>&venture=<?=e($reportVenture)?>&export=csv">⬇ CSV</a></div></div>
<form method="get" class="report-filters"><input type="hidden" name="page" value="reports"><label>Report type<select name="report_type"><option value="financial" <?=$reportType==='financial'?'selected':''?>>Financial Summary</option><option value="sales" <?=$reportType==='sales'?'selected':''?>>Sales Revenue</option><option value="expenses" <?=$reportType==='expenses'?'selected':''?>>Expenses</option></select></label><label>Venture<select name="venture"><option value="all" <?=$reportVenture==='all'?'selected':''?>>All ventures</option><option value="shop" <?=$reportVenture==='shop'?'selected':''?>>Shop</option><option value="crops" <?=$reportVenture==='crops'?'selected':''?>>Beans &amp; Onions Farm</option><option value="cattle" <?=$reportVenture==='cattle'?'selected':''?>>Cattle Farm</option></select></label><label>Start date<input type="date" name="start_date" value="<?=e($startDate)?>"></label><label>End date<input type="date" name="end_date" value="<?=e($endDate)?>"></label><button class="btn primary" type="submit">Generate Report</button><a class="btn" href="?page=reports">Clear</a></form>
<div class="report-grid"><div class="report-card"><b>Revenue</b><strong>MWK <?=number_format($reportIncome,2)?></strong></div><div class="report-card"><b>Expenses</b><strong>MWK <?=number_format($reportExpenses,2)?></strong></div><div class="report-card"><b>Net</b><strong>MWK <?=number_format($reportIncome-$reportExpenses,2)?></strong></div><div class="report-card"><b>Transactions</b><strong><?=number_format($reportCount)?></strong></div></div>
<div class="table-wrap report-table"><table><thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Category / Customer</th><th>Amount (MWK)</th></tr></thead><tbody><?php if(!$reportRows): ?><tr><td colspan="5" class="empty">No records match the selected report and date range.</td></tr><?php else: foreach($reportRows as $rr): ?><tr><td><?=e($rr['date'])?></td><td><?=e($rr['type'])?></td><td><?=e($rr['description'])?></td><td><?=e($rr['category'])?></td><td><?=number_format($rr['amount'],2)?></td></tr><?php endforeach;endif;?></tbody></table></div></div>

<?php elseif($page==='notifications'): ?>
<div class="panel"><div class="list-head"><div><h3>Notifications</h3><p class="muted">Live operational alerts calculated from the central database.</p></div><span class="pill"><?=number_format(count($notificationItems??[]))?> ACTIVE</span></div><?php if(empty($notificationItems)): ?><div class="empty-notification">✓ No active notifications. Your farm is up to date.</div><?php else: ?><div class="notification-grid"><?php foreach($notificationItems as $n): ?><div class="notification-card"><span><?=e($n['icon'])?></span><div><b><?=e($n['title'])?></b><small><?=e($n['type'])?> · <?=e($n['detail'])?></small></div></div><?php endforeach; ?></div><?php endif; ?></div>

<?php elseif($page==='settings'): ?>
<?php $settings=$pdo->query("SELECT setting_key,setting_value FROM settings ORDER BY setting_key")->fetchAll(); $sv=[];foreach($settings as $ss)$sv[$ss['setting_key']]=$ss['setting_value']; $users=$pdo->query("SELECT id,username,role,active,last_login_at FROM users ORDER BY username")->fetchAll(); ?>
<div class="settings-grid">
<div class="panel"><h3>Farm settings</h3><p class="muted">These values are stored in MySQL and shared across devices.</p>
<form method="post" class="form"><input type="hidden" name="_csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="save_setting">
<label>Setting key<input name="key" value="farm_name" required></label>
<label>Value<input name="value" value="<?=e($sv['farm_name']??'Farming with Urunji Farm')?>"></label>
<button class="btn primary">Save setting</button></form>
<div class="security-note"><b>Deployment note</b><br>Do not put database passwords in GitHub. Keep <code>config.php</code> private.</div>
<div class="backup-panel"><h3>Backup & Restore</h3><p class="muted">Export the central MySQL records to a portable JSON file, or restore a previous FWU JSON backup. The restore does not change user passwords or login sessions.</p>
<form method="post" class="backup-actions"><input type="hidden" name="_csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="export_backup"><button class="btn primary" type="submit">⬇ Export JSON backup</button></form>
<?php if(app_is_admin()): ?><form method="post" enctype="multipart/form-data" class="backup-restore" onsubmit="return confirm('This will replace the current farm records, settings and stock ledger in MySQL. Continue?');"><input type="hidden" name="_csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="import_backup"><label>Restore JSON backup<input type="file" name="backup_file" accept="application/json,.json" required></label><label>Type <b>RESTORE</b> to confirm<input name="confirm_restore" autocomplete="off" placeholder="RESTORE" required></label><button class="btn" type="submit">⬆ Restore backup</button></form><small class="muted">Compatible with the older FWU-BACKUP 1.0 local-storage JSON files and the new server backup format.</small><?php else: ?><small class="muted">Only administrators can restore backups. All authorised users can export a backup.</small><?php endif; ?></div>
</div>
<div class="danger-panel"><h3>Reset system data</h3><p class="muted">Use this when you want to replace the farm completely with an entirely new JSON backup. This removes all farm records, stock-ledger movements and saved settings, but keeps all user accounts.</p>
<?php if(app_is_admin()): ?><form method="post" class="backup-restore" onsubmit="return confirm('WARNING: This will permanently delete all farm records, stock ledger movements and saved settings. User accounts will remain. Continue?');"><input type="hidden" name="_csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="reset_system"><label>Type <b>RESET</b> to confirm<input name="confirm_reset" autocomplete="off" placeholder="RESET" required></label><button class="btn danger" type="submit">🗑 Reset System Data</button></form><?php endif; ?></div>
</div>
<div class="panel"><h3>Users</h3><p class="muted">Each authorised person can have a separate login. User creation and status changes are administrator-only; all users still work against the same central database.</p>
<form method="post" class="form grid2"><input type="hidden" name="_csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="add_user">
<label>Username<input name="new_username" required></label><label>Temporary password<input type="password" name="new_password" minlength="8" required></label>
<label>Role<select name="new_role"><option>worker</option><option>manager</option><option>accountant</option><option>admin</option></select></label><div></div>
<button class="btn primary">Create user</button></form>
<div class="table-wrap" style="margin-top:16px"><table><thead><tr><th>Username</th><th>Role</th><th>Status</th><th>Last login</th><th></th></tr></thead><tbody>
<?php foreach($users as $u): ?><tr><td><?=e($u['username'])?></td><td><?=e($u['role'])?></td><td><?=((int)$u['active']?'Active':'Disabled')?></td><td><?=e($u['last_login_at']??'Never')?></td><td><?php if((int)$u['id']!==(int)$_SESSION['user_id']): ?><form method="post"><input type="hidden" name="_csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="toggle_user"><input type="hidden" name="user_id" value="<?=e($u['id'])?>"><button class="btn"><?=((int)$u['active']?'Disable':'Enable')?></button></form><?php else: ?>Current user<?php endif;?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
</div>

<?php elseif(isset($MODULES[$page])): $cfg=$MODULES[$page]; ?>
<?php if($page==='finance'):
$financeRows=get_records($pdo,'finance'); $financeFilter=(string)($_GET['venture']??'all');
$financeTotals=['income'=>0.0,'expense'=>0.0,'owner_draw'=>0.0,'transfer'=>0.0];
$financeLedger=[];
$addFinanceLine=function($date,$venture,$type,$description,$amount,$source)use(&$financeLedger,&$financeTotals,$financeFilter){
 $venture=canonical_venture($venture?:'shop','finance'); if($financeFilter!=='all'&&$venture!==$financeFilter)return;
 $amount=clean_decimal($amount); if($amount<=0)return;
 $type=strtolower($type); if(!isset($financeTotals[$type]))$type='expense';
 $financeTotals[$type]+=$amount;
 $financeLedger[]=['date'=>(string)$date,'venture'=>$venture,'type'=>$type,'description'=>(string)$description,'amount'=>$amount,'source'=>$source];
};
// Load operational records in this module scope; dashboard variables are not guaranteed to exist here.
$salesRows=get_records($pdo,'sales');
$procurementRows=get_records($pdo,'procurement');
foreach($financeRows as $fr)$addFinanceLine($fr['transaction_date']??$fr['date']??'', $fr['venture']??'shop', $fr['transaction_type']??'expense', $fr['description']??'Finance entry', $fr['amount']??0, 'Group Finance');
foreach($salesRows as $r)$addFinanceLine($r['sale_date']??'', $r['venture']??'shop','income','Sale: '.($r['item_name']??'Product').' · '.($r['customer']??''),sale_total($r),'Sales');
foreach($procurementRows as $r)$addFinanceLine($r['purchase_date']??'', $r['venture']??'shop','expense','Procurement: '.($r['item_name']??'Item').' · '.($r['supplier']??''),procurement_total($r),'Procurement');
foreach(get_records($pdo,'advisory') as $r){
 $v=$r['venture']??'shop'; $d=$r['engagement_title']??$r['client_name']??'Advisory service';
 $addFinanceLine($r['start_date']??$r['due_date']??'', $v,'income','Advisory payment: '.$d,$r['amount_paid']??0,'Advisory');
 $addFinanceLine($r['start_date']??$r['due_date']??'', $v,'expense','Advisory project cost: '.$d,$r['project_cost']??0,'Advisory');
}
foreach(get_records($pdo,'veterinary') as $r)$addFinanceLine($r['treatment_date']??'', $r['venture']??'cattle','expense','Veterinary: '.($r['medicine']??'Treatment'),$r['cost']??0,'Veterinary');
usort($financeLedger,fn($a,$b)=>strcmp($b['date'],$a['date']));
$ventureNames=['all'=>'Group Overview','shop'=>'Shop','crops'=>'Beans & Onions Farm','cattle'=>'Cattle Farm'];
$records=array_values(array_filter($records,function($r)use($financeFilter){return $financeFilter==='all'||canonical_venture($r['venture']??'shop','finance')===$financeFilter;}));
?>
<div class="panel venture-finance">
 <div class="list-head"><div><h3>Financial performance by venture</h3><p class="muted">Separate finance records for each operation, with a combined group view.</p></div></div>
 <div class="venture-tabs"><?php foreach($ventureNames as $vk=>$vl): ?><a class="btn <?=($financeFilter===$vk?'primary':'')?>" href="?page=finance&venture=<?=$vk?>"><?=e($vl)?></a><?php endforeach; ?></div>
 <div class="stats-grid finance-summary">
  <div class="stat-card"><span>Recorded income</span><strong>MWK <?=number_format($financeTotals['income'],2)?></strong></div>
  <div class="stat-card"><span>Recorded expenses</span><strong>MWK <?=number_format($financeTotals['expense'],2)?></strong></div>
  <div class="stat-card"><span>Net recorded result</span><strong>MWK <?=number_format($financeTotals['income']-$financeTotals['expense'],2)?></strong></div>
 </div>
 <p class="muted">Unified ledger combines manual Group Finance entries with automatically collected Sales, Procurement, Advisory payments/costs and Veterinary costs. Operational entries are shown here for reporting without requiring duplicate entry. Transfers and owner withdrawals remain separate from operating income/expenses.</p>
 <div class="list-head"><div><h3>All transactions</h3><p class="muted"><?=number_format(count($financeLedger))?> combined records · newest first</p></div><button class="btn primary" type="button" id="export-finance-csv">⬇ Export report CSV</button></div>

 <div class="table-wrap finance-ledger-wrap" id="finance-ledger-table"><table><thead><tr><th>Date</th><th>Venture</th><th>Type</th><th>Description</th><th>Amount (MWK)</th><th>Source</th></tr></thead><tbody><?php if(!$financeLedger): ?><tr><td colspan="6" class="empty">No financial transactions yet. Record sales, procurement, service payments or a manual income/expense to populate this report.</td></tr><?php else: foreach($financeLedger as $fl): ?><tr><td><?=e($fl['date'])?></td><td><?=e($ventureNames[$fl['venture']]??ucfirst($fl['venture']))?></td><td><?=e(ucwords(str_replace('_',' ',$fl['type'])))?></td><td><?=e($fl['description'])?></td><td><?=number_format($fl['amount'],2)?></td><td><?=e($fl['source'])?></td></tr><?php endforeach;endif;?></tbody></table></div>
 <script>
 (function(){const btn=document.getElementById('export-finance-csv'),table=document.querySelector('#finance-ledger-table table');if(!btn||!table)return;btn.addEventListener('click',function(){const rows=Array.from(table.querySelectorAll('tr')).map(tr=>Array.from(tr.querySelectorAll('th,td')).map(td=>'"'+td.innerText.replace(/"/g,'""').trim()+'"').join(','));const blob=new Blob(['\\ufeff'+rows.join('\\r\\n')],{type:'text/csv;charset=utf-8;'});const url=URL.createObjectURL(blob);const a=document.createElement('a');a.href=url;a.download='FWU-group-finance-'+new Date().toISOString().slice(0,10)+'.csv';a.click();URL.revokeObjectURL(url);});})();
 </script>
</div>
<?php endif; ?>
<div class="module-layout">
<div class="panel form-panel">
<h3><?=isset($edit)?'Edit':'Add'?> <?=$cfg['label']?> record</h3><?php if($page==='inventory'): ?><div class="integration"><b>Stock entry:</b> Item names are matched without case sensitivity. Adding an existing item in the same venture and unit increases its current quantity instead of creating a duplicate. Shop, farm and cattle stock remain separate.</div><?php endif; ?>
<p class="muted"><?=isset($edit)?'Update the record; the change is written directly to MySQL.':'New records are saved directly to MySQL.'?></p>
<form method="post" class="form grid2" id="record-form">
<input type="hidden" name="_csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="save_record"><input type="hidden" name="module" value="<?=e($page)?>">
<input type="hidden" name="uuid" value="<?=e($edit['row']['record_uuid']??'')?>">
<?php foreach($cfg['fields'] as $key=>$meta): $val=$edit['data'][$key]??''; if($page==='advisory')$val='shop'; if($key==='venture' && $val==='') $val=canonical_venture('', $page); $type=$meta[1]??'text'; ?>
<label class="<?=$type==='textarea'?'full':''?>"><?=e($meta[0])?><?php if(in_array('required',$meta,true)):?> <em>*</em><?php endif;
if($type==='textarea'): ?><textarea name="<?=e($key)?>" rows="3" <?=in_array('required',$meta,true)?'required':''?>><?=e($val)?></textarea>
<?php elseif($type==='select' && $key==='venture' && $page==='advisory'): ?><input type="hidden" name="venture" value="shop"><span class="readonly-value">Shop (advisory services are tracked under Shop)</span><?php elseif($type==='select'): $options=match($key){'venture'=>['shop'=>'Shop','crops'=>'Beans & Onions Farm','cattle'=>'Cattle Farm'],'service_type'=>['crop_production'=>'Crop production advice','agribusiness'=>'Agribusiness and farm management','investment'=>'Investment and project development','training'=>'Training and capacity building','farm_visit'=>'Farm visit / field assessment','livestock'=>'Livestock advisory','inputs'=>'Agricultural inputs guidance','market'=>'Market access and marketing','custom'=>'Other / custom client request'],'status'=>['new'=>'New enquiry','planned'=>'Planned','in_progress'=>'In progress','awaiting_client'=>'Awaiting client','completed'=>'Completed','cancelled'=>'Cancelled'],'salary_frequency'=>['monthly'=>'Monthly','weekly'=>'Weekly','daily'=>'Daily','contract'=>'Contract / Other'],'transaction_type'=>['income'=>'Income','expense'=>'Expense','owner_draw'=>'Owner withdrawal','transfer'=>'Internal transfer'],default=>[]}; ?>
<select name="<?=e($key)?>" <?=in_array('required',$meta,true)?'required':''?>><option value="">Select…</option><?php foreach($options as $ov=>$ol): ?><option value="<?=e($ov)?>" <?=$val===$ov?'selected':''?>><?=e($ol)?></option><?php endforeach; ?></select>
<?php else: ?><input type="<?=$type==='email'?'email':$type?>" name="<?=e($key)?>" value="<?=e($val)?>" <?=in_array('required',$meta,true)?'required':''?> step="<?=$type==='number'?'0.001':'1'?>" <?=($page==='sales'&&$key==='total_price')?'readonly id="sale-total"':''?>><?php endif; ?></label>
<?php endforeach; ?>
<div class="form-actions full"><button class="btn primary"><?=isset($edit)?'Update record':'Save record'?></button><?php if(isset($edit)): ?><a class="btn" href="?page=<?=$page?>">Cancel</a><?php endif;?></div>
</form>
<?php if($page==='sales'): ?><script>
(function(){
 const q=document.querySelector('[name="quantity"]'), p=document.querySelector('[name="unit_price"]'), t=document.getElementById('sale-total');
 function calc(){ if(!q||!p||!t)return; const total=(parseFloat(q.value)||0)*(parseFloat(p.value)||0); t.value=total.toFixed(2); }
 if(q)q.addEventListener('input',calc); if(p)p.addEventListener('input',calc); calc();
})();
</script><?php endif; ?>
<?php if(in_array($page,['procurement','sales'],true)): ?><div class="integration"><b>Stock integration:</b> <?=$page==='procurement'?'Saving this record increases inventory. Editing/deleting reverses the previous movement first.':'Saving this record decreases inventory. Editing/deleting reverses the previous movement first.'?></div><?php endif;?>
</div>
<div class="panel list-panel">
<div class="list-head"><div><h3>Venture records</h3><p class="muted"><?=number_format(count($records))?> records in MySQL</p></div><span class="live">● LIVE DATABASE</span></div>
<?php if(in_array($page,['inventory','procurement','sales','crops','harvests','livestock','veterinary','equipment','fields','finance','advisory','customers','suppliers'],true)): $activeVenture=(string)($_GET['venture']??'all'); ?>
<div class="venture-tabs module-venture-tabs"><a class="btn <?=($activeVenture==='all'?'primary':'')?>" href="?page=<?=$page?>">All ventures</a><a class="btn <?=($activeVenture==='shop'?'primary':'')?>" href="?page=<?=$page?>&venture=shop">Shop</a><a class="btn <?=($activeVenture==='crops'?'primary':'')?>" href="?page=<?=$page?>&venture=crops">Beans &amp; Onions Farm</a><a class="btn <?=($activeVenture==='cattle'?'primary':'')?>" href="?page=<?=$page?>&venture=cattle">Cattle Farm</a></div>
<?php endif; ?>
<div class="table-wrap"><table><thead><tr><?php foreach($cfg['fields'] as $meta): ?><th><?=e($meta[0])?></th><?php endforeach; ?><th>Updated</th><th></th></tr></thead><tbody>
<?php if(!$records): ?><tr><td colspan="<?=count($cfg['fields'])+2?>" class="empty">No records yet.</td></tr><?php else: foreach($records as $r): ?><tr>
<?php foreach($cfg['fields'] as $key=>$meta): ?><td><?php if($page==='sales'&&$key==='total_price'): ?><b>MWK <?=number_format(sale_total($r),2)?></b><?php else: ?><?=e(is_scalar($r[$key]??'')?(string)($r[$key]??''):'')?><?php endif; ?></td><?php endforeach; ?>
<td><small><?=e($r['_updated_at']??'')?></small></td><td class="actions"><a href="?page=<?=$page?>&edit=<?=e($r['_uuid'])?>">Edit</a><form method="post" onsubmit="return confirm('Delete this record?');"><input type="hidden" name="_csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="delete_record"><input type="hidden" name="module" value="<?=$page?>"><input type="hidden" name="uuid" value="<?=e($r['_uuid'])?>"><button>Delete</button></form></td>
</tr><?php endforeach; endif; ?></tbody></table></div></div>
</div>
<?php endif; ?>
</div>
<footer>© <?=date('Y')?> Farming with Urunji · FWU · Farming with Urunji · Owner: Urunji Mezuwa</footer>
</main></div>
<?php endif; ?>
</body></html>

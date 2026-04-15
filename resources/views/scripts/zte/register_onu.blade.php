conf t
! Masuk ke Interface GPON
interface gpon-olt_{{ $frame }}/{{ $slot }}/{{ $port }}
! Daftarkan ONU dengan SN
onu {{ $onu_index }} type {{ $ont_type }} sn {{ $serial_number }}
exit

! Konfigurasi ONU Interface
interface gpon-onu_{{ $frame }}/{{ $slot }}/{{ $port }}:{{ $onu_index }}
name {{ $description }}
! Setup T-CONT (Bandwidth)
tcont 1 profile {{ $tcont_profile_name }}
! Setup GEM Port
gemport 1 name Internet tcont 1
gemport 1 traffic-limit upstream {{ $traffic_profile_name }} downstream {{ $traffic_profile_name }}

! Setup VLAN pada sisi ONU (Trunk/Access)
switchport mode hybrid vport 1
service-port 1 vport 1 user-vlan {{ $vlan_id }} vlan {{ $vlan_id }}
exit

! Konfigurasi Service Port (Sisi OLT) untuk bridging ke Mikrotik
pon-onu-mng gpon-onu_{{ $frame }}/{{ $slot }}/{{ $port }}:{{ $onu_index }}
service-port 1 gemport 1 match vlan {{ $vlan_id }}
vlan port {{ $vlan_id }} mode tag vlan {{ $vlan_id }}
exit

! Simpan Konfigurasi
write
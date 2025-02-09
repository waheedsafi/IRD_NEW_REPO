<?php

namespace App\Repositories\ngo;

use App\Models\Ngo;
use App\Traits\Ngo\NgoTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\Address\AddressTrait;

class NgoRepository implements NgoRepositoryInterface
{
    use AddressTrait, NgoTrait;

    public function startRegisterFormInfo($query, $ngo_id, $locale)
    {
        $this->typeTransJoin($query, $locale)
            ->emailJoin($query)
            ->contactJoin($query)
            ->addressJoin($query);
        $ngo = $query->select(
            'n.abbr',
            'n.ngo_type_id',
            'ntt.value as type_name',
            'n.registration_no',
            'n.moe_registration_no',
            'n.place_of_establishment',
            'n.date_of_establishment',
            'a.province_id',
            'a.district_id',
            'a.id as address_id',
            'e.value as email',
            'c.value as contact'
        )->first();

        if (!$ngo)
            return null;

        // Fetching translations using a separate query
        $translations = $this->ngoNameTrans($ngo_id);
        $areaTrans = $this->getAddressAreaTran($ngo->address_id);
        $address = $this->getAddressTrans(
            $ngo->province_id,
            $ngo->district_id,
            $locale
        );

        return [
            'name_english' => $translations['en']->name ?? null,
            'name_pashto' => $translations['ps']->name ?? null,
            'name_farsi' => $translations['fa']->name ?? null,
            'abbr' => $ngo->abbr,
            'type' => ['name' => $ngo->type_name, 'id' => $ngo->ngo_type_id],
            'contact' => $ngo->contact,
            'email' =>   $ngo->email,
            'registration_no' => $ngo->registration_no,
            'province' => $address['province'],
            'district' => $address['district'],
            'area_english' => $areaTrans['en']->area ?? '',
            'area_pashto' => $areaTrans['ps']->area ?? '',
            'area_farsi' => $areaTrans['fa']->area ?? '',
        ];
    }
    public function afterRegisterFormInfo($query, $ngo_id, $locale)
    {
        $this->typeTransJoin($query, $locale)
            ->emailJoin($query)
            ->contactJoin($query)
            ->addressJoin($query);
        $ngo = $query->select(
            'n.abbr',
            'n.is_editable',
            'n.ngo_type_id',
            'ntt.value as type_name',
            'n.registration_no',
            'n.moe_registration_no',
            'n.place_of_establishment',
            'n.date_of_establishment',
            'a.province_id',
            'a.district_id',
            'a.id as address_id',
            'e.value as email',
            'c.value as contact'
        )->first();

        if (!$ngo)
            return null;

        // Fetching translations using a separate query
        $translations = $this->ngoNameTrans($ngo_id);
        $areaTrans = $this->getAddressAreaTran($ngo->address_id);
        $address = $this->getAddressTrans(
            $ngo->province_id,
            $ngo->district_id,
            $locale
        );

        return [
            'name_english' => $translations['en']->name ?? null,
            'name_pashto' => $translations['ps']->name ?? null,
            'name_farsi' => $translations['fa']->name ?? null,
            'abbr' => $ngo->abbr,
            'registration_no' => $ngo->registration_no,
            'moe_registration_no' => $ngo->moe_registration_no,
            'date_of_establishment' => $ngo->date_of_establishment,
            'type' => ['name' => $ngo->type_name, 'id' => $ngo->ngo_type_id],
            'establishment_date' => $ngo->date_of_establishment,
            'place_of_establishment' => ['name' => $this->getCountry($ngo->place_of_establishment, $locale), 'id' => $ngo->place_of_establishment],
            'contact' => $ngo->contact,
            'email' => $ngo->email,
            'province' => $address['province'],
            'district' => $address['district'],
            'area_english' => $areaTrans['en']->area ?? '',
            'area_pashto' => $areaTrans['ps']->area ?? '',
            'area_farsi' => $areaTrans['fa']->area ?? '',
        ];
    }
    // Joins
    public function ngo($id = null)
    {
        if ($id) {
            return DB::table('ngos as n')->where('n.id', $id);
        } else {
            return DB::table('ngos as n');
        }
    }
    public function transJoin($query, $locale)
    {
        $query->join('ngo_trans as nt', function ($join) use ($locale) {
            $join->on('nt.ngo_id', '=', 'n.id')
                ->where('nt.language_name', $locale);
        });
        return $this;
    }
    public function statusJoin($query)
    {
        $query->leftjoin('ngo_statuses as ns', function ($join) {
            $join->on('ns.ngo_id', '=', 'n.id')
                ->whereRaw('ns.created_at = (select max(ns2.created_at) from ngo_statuses as ns2 where ns2.ngo_id = n.id)');
        });
        return $this;
    }
    public function statusTypeTransJoin($query, $locale)
    {
        $query->leftjoin('status_type_trans as stt', function ($join) use ($locale) {
            $join->on('stt.status_type_id', '=', 'ns.status_type_id')
                ->where('stt.language_name', $locale);
        });
        return $this;
    }
    public function typeTransJoin($query, $locale)
    {
        $query->join('ngo_type_trans as ntt', function ($join) use ($locale) {
            $join->on('ntt.ngo_type_id', '=', 'n.ngo_type_id')
                ->where('ntt.language_name', $locale);
        });
        return $this;
    }
    public function directorJoin($query)
    {
        $query->leftJoin('directors as d', function ($join) {
            $join->on('d.ngo_id', '=', 'n.id')
                ->where('d.is_active', true);
        });
        return $this;
    }
    public function directorTransJoin($query, $locale)
    {
        $query->leftJoin('director_trans as dt', function ($join) use ($locale) {
            $join->on('d.id', '=', 'dt.director_id')
                ->where('dt.language_name', $locale);
        });
        return $this;
    }
    public function emailJoin($query)
    {
        $query->leftJoin('emails as e', 'e.id', '=', 'n.email_id');
        return $this;
    }
    public function contactJoin($query)
    {
        $query->leftJoin('contacts as c', 'c.id', '=', 'n.contact_id');
        return $this;
    }
    public function addressJoin($query)
    {
        $query->leftJoin('addresses as a', 'a.id', '=', 'n.address_id');
        return $this;
    }
}

<?php

namespace HessamDev\Hessam\Requests;


class DeleteHessamPostRequest  extends BaseRequest {


    /**
     * No rules needed for this DELETE request - we just need to implement it due to the interface requirement
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
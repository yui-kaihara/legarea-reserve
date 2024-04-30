<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Company;

class CompanyService
{
    /**
     * 保存
     * 
     * @param array $requests
     * @return Company
     */
    public function store(array $requests)
    {
        $company = new Company();
        return $company->create($requests);
    }
    
    /**
     * 更新
     * 
     * @param array $requests
     * @param int $id
     * @return void
     */
    public function update(array $requests, int $id)
    {
        $company = Company::find($id);
        $company->fill($requests)->save();
    }
    
    /**
     * 削除
     * 
     * @param int $id
     * @return void
     */
    public function destroy(int $id)
    {
        $company = Company::find($id);
        
        //参加回数と交流会IDをマイナス1
        $requests = [
            'count' => $company->count - 1,
            'event_id' => $company->event_id - 1
        ];

        //更新処理
        $this->update($requests, $id);
    }
}

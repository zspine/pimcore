<?php
/**
 * Pimcore
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @category   Pimcore
 * @package    Staticroute
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace Pimcore\Model\Staticroute;

use Pimcore\Model;

class Dao extends Model\Dao\PhpArrayTable
{

    /**
     *
     */
    public function configure()
    {
        parent::configure();
        $this->setFile("staticroutes");
    }

    /**
     * @param null $id
     * @throws \Exception
     */
    public function getById($id = null)
    {
        if ($id != null) {
            $this->model->setId($id);
        }

        $data = $this->db->getById($this->model->getId());

        if (isset($data["id"])) {
            $this->assignVariablesToModel($data);
        } else {
            throw new \Exception("Route with id: " . $this->model->getId() . " does not exist");
        }
    }

    /**
     * @param null $name
     * @param null $siteId
     * @throws \Exception
     */
    public function getByName($name = null, $siteId = null)
    {
        if ($name != null) {
            $this->model->setName($name);
        }

        $name = $this->model->getName();

        $data = $this->db->fetchAll(function ($row) use ($name, $siteId) {
            if ($row["name"] == $name) {
                if (empty($row["siteId"]) || $row["siteId"] == $siteId) {
                    return true;
                }
            }
            return false;
        }, function ($a, $b) {
            if ($a["siteId"] == $b["siteId"]) {
                return 0;
            }
            return ($a["siteId"] < $b["siteId"]) ? 1 : -1;
        });

        if (count($data) && $data[0]["id"]) {
            $this->assignVariablesToModel($data[0]);
        } else {
            throw new \Exception("Route with name: " . $this->model->getName() . " does not exist");
        }
    }

    /**
     * @throws \Exception
     */
    public function save()
    {
        $ts = time();
        if (!$this->model->getCreationDate()) {
            $this->model->setCreationDate($ts);
        }
        $this->model->setModificationDate($ts);

        try {
            $dataRaw = get_object_vars($this->model);
            $data = [];
            $allowedProperties = ["id","name","pattern","reverse","module","controller",
                "action","variables","defaults","siteId","priority","creationDate","modificationDate"];

            foreach ($dataRaw as $key => $value) {
                if (in_array($key, $allowedProperties)) {
                    $data[$key] = $value;
                }
            }
            $this->db->insertOrUpdate($data, $this->model->getId());
        } catch (\Exception $e) {
            throw $e;
        }

        if (!$this->model->getId()) {
            $this->model->setId($this->db->getLastInsertId());
        }
    }

    /**
     * Deletes object from database
     *
     * @return void
     */
    public function delete()
    {
        $this->db->delete($this->model->getId());
    }
}

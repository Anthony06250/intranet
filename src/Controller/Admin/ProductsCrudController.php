<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\Field\MoneyField;
use App\Form\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductsCrudController extends AbstractCrudController
{
    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('label', 'Forms.Labels.Product');
        yield TextField::new('serialNumber', 'Forms.Labels.Serial number');

        if ($pageName !== 'embedded_fields_without_barcode') {
            yield TextField::new('barCode', 'Forms.Labels.Bar code');
        }

        yield MoneyField::new('price', 'Forms.Labels.Product price');
    }
}

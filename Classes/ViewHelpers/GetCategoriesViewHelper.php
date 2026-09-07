<?php

declare(strict_types=1);

namespace WSR\Myleaflet\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use WSR\Myleaflet\Domain\Repository\CategoryRepository;

final class GetCategoriesViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository
        ) {
    }
    
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        
        $this->registerArgument(
            'parentCategory',
            'int',
            'The parent category',
            true,
            0
            );
        
        $this->registerArgument(
            'excludeCategories',
            'string',
            'Exclude categories',
            false,
            ''
            );
        
        $this->registerArgument(
            'as',
            'string',
            'Name of the template variable that will contain the categories',
            true
            );
    }
    
    public function render(): string
    {
        $parentCategoryUid =
        (int)$this->arguments['parentCategory'];
        
        $excludeCategoriesString =
        trim((string)($this->arguments['excludeCategories'] ?? ''));
        
        $as =
        (string)$this->arguments['as'];
        
        $excludeCategories = [];
        
        if ($excludeCategoriesString !== '') {
            $excludeCategories = array_filter(
                array_map(
                    'intval',
                    explode(',', $excludeCategoriesString)
                    )
                );
        }
        
        $parent =
        $this->categoryRepository->findByUid(
            $parentCategoryUid
            );
        
        if ($parent === null) {
            return '';
        }
        
        $children =
        $this->categoryRepository->findChildrenByParent(
            $parentCategoryUid,
            $excludeCategories
            );
        
        $options = [
            0 => $parent->getTitle(),
        ];
        
        foreach ($children as $child) {
            $options[$child->getUid()] =
            $child->getTitle();
        }
        
        $variableProvider =
        $this->renderingContext->getVariableProvider();
        
        $variableProvider->add(
            $as,
            [
                'parent' => $parent,
                'children' => $children,
                'options' => $options,
            ]
            );
        
        try {
            return (string)$this->renderChildren();
        } finally {
            $variableProvider->remove($as);
        }
    }
}
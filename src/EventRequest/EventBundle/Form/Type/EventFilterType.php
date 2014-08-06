<?php

namespace EventRequest\EventBundle\Form\Type;

use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EventRequest\EventBundle\Entity\Event;

class EventFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'filter_text', array(
                    'apply_filter' => function(QueryInterface $filterQuery, $field, $values) {
                            if (!empty($values['value'])) {
                                $qb = $filterQuery->getQueryBuilder();
                                $qb->andWhere($filterQuery->getExpr()->like($field, '\'%'.$values['value'].'%\''));
                            }
                        },
                ))
            ->add('status', 'filter_choice', array(
                    'choices' => Event::getStatusList()
                ))
            ->add('city', 'filter_entity', array(
                    'class' => 'EventRequestEventBundle:City',
                    'property' => 'name',
                ))
            ->add('date', 'filter_date_range')
            ->add('save', 'submit')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'csrf_protection'   => false,
                'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'eventFilterType';
    }
}
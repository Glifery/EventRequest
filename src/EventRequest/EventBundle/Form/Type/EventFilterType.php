<?php

namespace EventRequest\EventBundle\Form\Type;

use EventRequest\EventBundle\Entity\Country;
use EventRequest\EventBundle\Form\EventListener\CitySubscriber;
use EventRequest\EventBundle\Form\EventListener\CountrySubscriber;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
            ->setMethod('GET')
            ->add('name', 'filter_text', array(
                    'label' => 'event.field.name',
                    'apply_filter' => function(QueryInterface $filterQuery, $field, $values) {
                            if (!empty($values['value'])) {
                                $qb = $filterQuery->getQueryBuilder('e');
                                $qb->
                                    andWhere($filterQuery->getExpr()->like($field, '\'%'.$values['value'].'%\''))
                                ;
                            }
                        }
                ))
            ->add('status', 'filter_choice', array(
                    'label' => 'event.field.status',
                    'choices' => Event::getStatusList()
                ))
            ->add('country', 'filter_entity', array(
                    'label' => 'event.field.country',
                    'class' => 'EventRequestEventBundle:Country',
                    'property' => 'name',
                    'empty_value' => 'page.filter.empty',
                    'apply_filter' => function(QueryInterface $filterQuery, $field, $values) {
                            if (!empty($values['value'])) {
                                $qb = $filterQuery->getQueryBuilder('e');
                                $qb
                                    ->innerJoin('e.city', 'city')
                                    ->andWhere($filterQuery->getExpr()->eq('city.country', ':country'))
                                    ->setParameter('country', $values['value'])
                                ;
                            }
                        }
                ))
            ->add('date', 'filter_date_range', array(
                    'label' => 'event.field.date',
                    'left_date_options' => array(
                        'widget' => 'single_text',
                        'format' =>'dd.MM.yyyy'
                    ),
                    'right_date_options' => array(
                        'widget' => 'single_text',
                        'format' =>'dd.MM.yyyy'
                    )
                ))
            ->add('save', 'submit', array(
                    'label' => 'event.filter'
                ))
        ;

        $formModifier = function (FormInterface $form, Country $country = null) {
            $cities = null === $country ? array() : $country->getCities();

            $form->add('city', 'entity', array(
                    'class'       => 'EventRequestEventBundle:City',
                    'property'    => 'name',
                    'empty_value' => 'page.filter.empty',
                    'choices'     => $cities,
                    'required' => false,
                    'label' => 'event.field.city',
                ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                /** @var City $city */
                $city = $event->getData();

                if ($city) {
                    $formModifier($event->getForm(), $city->getCountry());
                }

                $formModifier($event->getForm(), null);
            }
        );

        $builder->get('country')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                /** @var Country $country */
                $country = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $country);
            }
        );
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